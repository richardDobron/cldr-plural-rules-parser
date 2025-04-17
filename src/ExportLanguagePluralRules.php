<?php

namespace dobron\CLDRSupplementalData;

class ExportLanguagePluralRules
{
    protected const CLDR_BASE_URL = 'https://www.unicode.org/cldr/charts/latest/supplemental';
    protected const CLDR_DATA_URL = self::CLDR_BASE_URL . '/language_plural_rules.html';
    protected const CLDR_VERSION_URL = self::CLDR_BASE_URL . '/include-version.html';
    protected const RE_LANGUAGE_RELATION = '/=(\w+)/';
    protected const RE_NOT_AVAILABLE = '/n\/a|Not available/';

    protected array $table = [];
    protected ?int $highestColumn = null;
    protected ?string $version = null;
    protected array $languages = [];
    protected array $equalLanguages = [];

    public function __construct()
    {
        $this->checkDataVersion();

        $html = $this->fetch(self::CLDR_DATA_URL);

        $rows = $html->getElementsByTagName('tr');

        foreach ($rows as $rowIndex => $row) {
            $columns = $this->getElementsByClassName($row, 'td', 'dtf-s');
            if (count($columns)) {
                $this->parseRow($columns, $rowIndex);
            }
        }

        $this->build();
    }

    public function flush(): string
    {
        return json_encode([
            'version' => $this->version,
            'languages' => $this->languages,
            'equals' => $this->equalLanguages
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

    protected function build(): void
    {
        foreach ($this->table as $row) {
            [$languageName, $languageCode, $languageType, $languageCategory] = $row;

            if (empty($languageType)) {
                continue;
            }

            preg_match(self::RE_LANGUAGE_RELATION, $languageType, $languageModeMatch);

            if ($languageModeMatch) {
                $this->equalLanguages[$languageModeMatch[1]] ??= [];
                $this->equalLanguages[$languageModeMatch[1]][] = $languageCode;
                continue;
            }

            if (empty($languageCategory)) {
                continue;
            }

            $this->languages[$languageCode]['language'] = $languageName;

            $this->languages[$languageCode][$languageType][$languageCategory] = [
                'examples' => $row[4],
                'pairs' => $row[5],
                'rules' => $row[6]
            ];
        }
    }

    protected function fetch(string $url): \DOMDocument
    {
        $htmlContent = file_get_contents($url);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        @$dom->loadHTML($htmlContent);

        return $dom;
    }

    protected function checkDataVersion(): void
    {
        $html = $this->fetch(self::CLDR_VERSION_URL);
        $xpath = new \DOMXPath($html);

        $versionNode = $xpath->query("//span[contains(@class, 'version')]");
        if ($versionNode && $versionNode->length > 0) {
            $this->version = trim($versionNode->item(0)->nodeValue);
        }
    }

    protected function rowspan(\DOMElement $column): int
    {
        return (int) $column->getAttribute('rowspan') ?: 1;
    }

    protected function addValue(int $rowIndex, int $columnIndex, ?string $value): void
    {
        while (count($this->table) <= $rowIndex) {
            $this->table[] = array_fill(0, $this->highestColumn, '');
        }

        while (!empty($this->table[$rowIndex][$columnIndex])) {
            $columnIndex += 1;
        }

        $this->table[$rowIndex][$columnIndex] = $value;
    }

    protected function parseRow(array $columns, int $rowIndex): void
    {
        $this->highestColumn ??= count($columns);

        foreach ($columns as $columnIndex => $column) {
            while (count($this->table) <= $rowIndex) {
                $this->table[] = array_fill(0, $this->highestColumn, null);
            }

            $data = $this->getNodeValueWithLineBreaks($column);
            if (preg_match(self::RE_NOT_AVAILABLE, $data)) {
                $data = null;
            }

            $rowspan = $this->rowspan($column);

            if ($rowspan > 1) {
                foreach (range(0, $rowspan - 1) as $rowspanIndex) {
                    $this->addValue($rowIndex + $rowspanIndex, $columnIndex, $data);
                }
            } else {
                $this->addValue($rowIndex, $columnIndex, $data);
            }
        }
    }

    protected function getElementsByClassName(\DOMElement $element, string $tagName, string $className): array
    {
        $elements = [];

        foreach ($element->getElementsByTagName($tagName) as $child) {
            if ($child->hasAttribute('class') && str_contains($child->getAttribute('class'), $className)) {
                $elements[] = $child;
            }
        }

        return $elements;
    }

    protected function getNodeValueWithLineBreaks(\DOMElement $node): string
    {
        $textContent = '';

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $textContent .= $child->nodeValue;
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                if ($child->nodeName === 'br') {
                    $textContent .= "\r\n";
                } else {
                    $textContent .= $this->getNodeValueWithLineBreaks($child);
                }
            }
        }

        return html_entity_decode($textContent);
    }
}
