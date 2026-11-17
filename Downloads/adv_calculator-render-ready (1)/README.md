# DSA Advanced Calculator (Java + PHP)

```
Browser (index.php UI)  --fetch JSON-->  api.php (PHP)  --stdin-->  Java engine (AdvancedCalculator.class)
        answer  <------------------------  JSON  <-----------------  {"ok":true,"result":"15"}
```

- **Java** does all the math: tokenizer -> recursive-descent parser -> evaluator (BigDecimal / BigInteger for exact results).
- **PHP** renders the page, validates input, calls Java, and returns JSON.

## Requirements
- JDK 11 or newer (needs `javac`) and PHP 8.0+ (XAMPP works).

## Setup
1. Compile the engine from the project folder:
   ```
   mkdir java\bin            (Windows)    |   mkdir -p java/bin   (Linux/Mac)
   javac -d java/bin java/src/AdvancedCalculator.java
   ```
2. Run the app, either:
   - copy the folder into `xampp/htdocs/` and open `http://localhost/dsa-calculator/`, or
   - from the project folder: `php -S localhost:8000` then open `http://localhost:8000`.
3. If Java is not on your PATH, set `JAVA_BIN` at the top of `api.php` to the full path of `java.exe`.

## Formula syntax
| Feature | Type this |
|---|---|
| + − × ÷ | `+ - * /` (or the on-screen buttons) |
| Modulo / integer division | `17 % 5`, `17 // 5` (floor-based) |
| Floor / ceiling | `floor(3.7)`, `ceil(3.2)` |
| Power, roots | `2^10`, `√(16)`, `sqrt(16)`, `∛(27)`, `cbrt(27)` |
| Logs | `log(1000)` (base 10), `log2(8)`, `ln(x)` |
| Factorials | `5!`, `5!+3!`, `6!/4!` |
| Summation / product | `Σ(i=1 to 5) i`, `Π(i=1 to 4) i` |
| Double | `ΣΣ(i=1 to 3, j=1 to 3) i*j`, `ΠΠ(i=1 to 3, j=1 to 2) (i+j)` |
| Variables | assign a number to A, B, C, D (or x, y, c), then `xy`, `x+y`, `x^y`, `Cx`, `x+c`, `x^c` |

Notes
- A Σ/Π body covers one term, so use parentheses for sums inside it: `Σ(i=1 to 4) (i^2+1)`.
- Function arguments use parentheses: `√(x+1)`, `log(100)`.
- Invalid input (division by zero, undefined variable, negative factorial, incomplete formula, ...) shows a clear error instead of crashing.

## Test before the defense
`Σ(i=1 to 5) i` = 15, `Π(i=1 to 4) i` = 24, `5!` = 120, `6!/4!` = 30, `17 % 5` = 2, `2^-2` = 0.25, `log2(1024)` = 10.
