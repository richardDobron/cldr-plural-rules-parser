# CLDR Plural Rules Parser

Export language plural rules from CLDR Supplemental Data.

**Source Data (latest):** https://cldr.unicode.org/index/downloads

---

## 📦 Installation

Install the library using Composer:

```shell
$ composer require dobron/cldr-plural-rules-parser
```

## ⚡️ Quick Start

### Flush JSON Response
```php
echo (new \dobron\CLDRSupplementalData\ExportLanguagePluralRules)->flush();
```

### Load Specific Version
```php
echo (new \dobron\CLDRSupplementalData\ExportLanguagePluralRules)->load('v40');
```

### Save JSON File
```php
(new \dobron\CLDRSupplementalData\ExportLanguagePluralRules)->store('latest.json');
```

## Example Data
Here is an example of the JSON output:

```json5
{
  "version": "40.0β",
  "languages": {
    // ...
    "en": {
      "language": "English",
      "cardinal": {
        "one": {
          "examples": "1",
          "pairs": "1 day",
          "rules": "i = 1 and\r\n   v = 0"
        },
        "other": {
          "examples": "0, 2~16, 100, 1000, 10000, 100000, 1000000, … 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
          "pairs": "2 days\r\n1.5 days",
          "rules": ""
        }
      },
      "ordinal": {
        "one": {
          "examples": "1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, …",
          "pairs": "Take the 1st right.",
          "rules": "n % 10 = 1 and\r\n   n % 100 != 11"
        },
        "two": {
          "examples": "2, 22, 32, 42, 52, 62, 72, 82, 102, 1002, …",
          "pairs": "Take the 2nd right.",
          "rules": "n % 10 = 2 and\r\n   n % 100 != 12"
        },
        "few": {
          "examples": "3, 23, 33, 43, 53, 63, 73, 83, 103, 1003, …",
          "pairs": "Take the 3rd right.",
          "rules": "n % 10 = 3 and\r\n   n % 100 != 13"
        },
        "other": {
          "examples": "0, 4~18, 100, 1000, 10000, 100000, 1000000, …",
          "pairs": "Take the 4th right.",
          "rules": ""
        }
      },
      "range": {
        "one+other": {
          "examples": "1–2",
          "pairs": "1–2 days",
          "rules": "one + other → other"
        },
        "other+one": {
          "examples": "0–1",
          "pairs": "0–1 days",
          "rules": "other + one → other"
        },
        "other+other": {
          "examples": "0–2",
          "pairs": "0–2 days",
          "rules": "other + other → other"
        }
      }
    },
    "sk": {
      "language": "Slovak",
      "cardinal": {
        "one": {
          "examples": "1",
          "pairs": "1 deň",
          "rules": "i = 1 and\r\n   v = 0"
        },
        "few": {
          "examples": "2~4",
          "pairs": "2 dni",
          "rules": "i = 2..4 and\r\n   v = 0"
        },
        "many": {
          "examples": "0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
          "pairs": "1,5 dňa",
          "rules": "v != 0"
        },
        "other": {
          "examples": "0, 5~19, 100, 1000, 10000, 100000, 1000000, …",
          "pairs": "5 dní",
          "rules": ""
        }
      },
      "ordinal": {
        "other": {
          "examples": "0~15, 100, 1000, 10000, 100000, 1000000, …",
          "pairs": "Na 15. križovatke odbočte doprava.",
          "rules": ""
        }
      },
      "range": {
        "one+few": {
          "examples": "1–2",
          "pairs": "1 – 2 dni",
          "rules": "one + few → few"
        },
        "one+many": {
          "examples": "1–10.0",
          "pairs": "1 – 10,0 dňa",
          "rules": "one + many → many"
        },
        "one+other": {
          "examples": "1–5",
          "pairs": "1 – 5 dní",
          "rules": "one + other → other"
        },
        "few+few": {
          "examples": "2–4",
          "pairs": "2 – 4 dni",
          "rules": "few + few → few"
        },
        "few+many": {
          "examples": "2–10.0",
          "pairs": "2 – 10,0 dňa",
          "rules": "few + many → many"
        },
        "few+other": {
          "examples": "2–5",
          "pairs": "2 – 5 dní",
          "rules": "few + other → other"
        },
        "many+one": {
          "examples": "0.0–1",
          "pairs": "0,0 – 1 deň",
          "rules": "many + one → one"
        },
        "many+few": {
          "examples": "0.0–2",
          "pairs": "0,0 – 2 dni",
          "rules": "many + few → few"
        },
        "many+many": {
          "examples": "0.0–10.0",
          "pairs": "0,0 – 10,0 dňa",
          "rules": "many + many → many"
        },
        "many+other": {
          "examples": "0.0–5",
          "pairs": "0,0 – 5 dní",
          "rules": "many + other → other"
        },
        "other+one": {
          "examples": "0–1",
          "pairs": "0 – 1 deň",
          "rules": "other + one → one"
        },
        "other+few": {
          "examples": "0–2",
          "pairs": "0 – 2 dni",
          "rules": "other + few → few"
        },
        "other+many": {
          "examples": "0–10.0",
          "pairs": "0 – 10,0 dňa",
          "rules": "other + many → many"
        },
        "other+other": {
          "examples": "0–5",
          "pairs": "0 – 5 dní",
          "rules": "other + other → other"
        }
      }
    }
    // ...
  },
  "equals": {
    "he": [
      "iw"
    ],
    "id": [
      "in"
    ],
    "jv": [
      "jw"
    ],
    "ro": [
      "mo"
    ],
    "sr_Latn": [
      "sh"
    ],
    "fil": [
      "tl"
    ],
    "yi": [
      "ji"
    ]
  }
}
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## 🙋 Credits

- [Richard Dobroň][link-author]


## ⚖️ License
This repository is MIT licensed, as found in the [LICENSE](LICENSE) file.

[link-author]: https://github.com/richardDobron
