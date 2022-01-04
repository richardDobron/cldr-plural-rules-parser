<?php

namespace dobron\CLDRSupplementalData;

use KubAT\PhpSimple\HtmlDomParser;

class ExportLanguagePluralRules
{
    private const CLDR_BASE_URL = "https://unicode-org.github.io/cldr-staging/charts/latest/supplemental";
    private const CLDR_DATA_URL = self::CLDR_BASE_URL . "/language_plural_rules.html";
    private const CLDR_VERSION_URL = self::CLDR_BASE_URL . "/include-version.html";
    private const RE_LANGUAGE_RELATION = '/=(\w+)/';

    private array $table = [];
    private ?int $highest_column = null;
    private ?string $version = null;
    private array $languages = [];
    private array $equal_languages = [];

    public function __construct()
    {
        $this->checkDataVersion();

        $html = $this->fetch(self::CLDR_DATA_URL);
        $rows = $html->find("table.dtf-table", 0)->find("tr");

        foreach ($rows as $row_index => $row) {
            $columns = $row->find("td.dtf-s");
            if (count($columns)) {
                $this->parseRow($columns, $row_index);
            }
        }

        $this->build();
    }

    public function flush(): string
    {
        return json_encode([
            "version" => $this->version,
            "languages" => $this->languages,
            "equals" => $this->equal_languages
        ], JSON_PRETTY_PRINT);
    }

    /**
     * @throws \Exception
     */
    public function load(string $version): object
    {
        $filename = dirname(__DIR__) . '/cldr-json/' . $version . '.json';

        if (!file_exists($filename)) {
            throw new \Exception('Invalid version code (' . $version . ')');
        }

        return json_decode(file_get_contents($filename));
    }

    public function store(string $filename): int|false
    {
        return file_put_contents($filename, $this->flush());
    }

    private function build(): self
    {
        foreach ($this->table as $row) {
            [$language_name, $language_code, $language_type, $language_category] = $row;
            preg_match(self::RE_LANGUAGE_RELATION, $language_type, $language_mode_match);

            if ($language_mode_match) {
                $this->equal_languages[$language_mode_match[1]] ??= [];
                $this->equal_languages[$language_mode_match[1]][] = $language_code;
                continue;
            }

            if (empty($language_category)) {
                continue;
            }

            $this->languages[$language_code]['language'] = $language_name;

            $this->languages[$language_code][$language_type][$language_category] = [
                "examples" => $row[4],
                "pairs" => $row[5],
                "rules" => $row[6]
            ];
        }

        return $this;
    }

    private function fetch(string $url)
    {
        return HtmlDomParser::str_get_html(file_get_contents($url));
    }

    private function rowspan($column): int
    {
        $rowspan = intval($column->getAttribute("rowspan"));

        if (!$rowspan) {
            return 1;
        }

        return $rowspan;
    }

    private function checkDataVersion(): void
    {
        $html = $this->fetch(self::CLDR_VERSION_URL);
        $this->version = $html->find("span.version", 0)->innertext;
    }

    private function value(int $row_index, int $col_index, $value): void
    {
        while (count($this->table) <= $row_index) {
            $this->table[] = array_fill(0, $this->highest_column, '');
        }

        while (!empty($this->table[$row_index][$col_index])) {
            $col_index += 1;
        }

        $this->table[$row_index][$col_index] = $value;
    }

    private function parseRow(array $columns, int $row_index)
    {
        if ($this->highest_column === null) {
            $this->highest_column = count($columns);
        }

        foreach ($columns as $column_index => $column) {
            while (count($this->table) <= $row_index) {
                $this->table[] = array_fill(0, $this->highest_column, null);
            }

            $data = html_entity_decode($column->plaintext);
            if (preg_match("/n\/a|Not available/", $data)) {
                $data = null;
            }

            $rowspan = $this->rowspan($column);

            if ($rowspan > 1) {
                foreach (range(0, $rowspan - 1) as $rowspan_index) {
                    $this->value($row_index + $rowspan_index, $column_index, $data);
                }
            } else {
                $this->value($row_index, $column_index, $data);
            }
        }
    }
}