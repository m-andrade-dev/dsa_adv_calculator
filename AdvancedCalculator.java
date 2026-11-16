import java.io.*;
import java.math.*;
import java.nio.charset.StandardCharsets;
import java.util.*;

/**
 * Advanced Calculator engine (DSA Final Project).
 *
 * Input  (stdin, UTF-8): line 1 = formula, next lines = variable assignments "A=5"
 * Output (stdout)      : {"ok":true,"result":"15"}  or  {"ok":false,"error":"..."}
 *
 * Design: Tokenizer -> Recursive-descent parser -> tree of Node lambdas -> evaluation.
 * Grammar (lowest to highest precedence):
 *   expr    := term (('+'|'-') term)*
 *   term    := unary (('*'|'/'|'%'|'//'|'mod'|'div'|implicit) unary)*
 *   unary   := ('-'|'+') unary | power
 *   power   := postfix ('^' unary)?                 (right associative)
 *   postfix := primary '!'*
 *   primary := NUMBER | VARIABLE | '(' expr ')' | FUNC primary | SQRT primary
 *            | ('Σ'|'Π') '(' v '=' expr 'to' expr ')' term
 *            | ('ΣΣ'|'ΠΠ') '(' v '=' expr 'to' expr ',' w '=' expr 'to' expr ')' term
 */
public class AdvancedCalculator {

    static final MathContext MC = new MathContext(60, RoundingMode.HALF_UP);
    static final int MAX_FACTORIAL = 3000;
    static final int MAX_EXPONENT = 20000;
    static final long MAX_ITERATIONS = 2_000_000L;

    static class CalcException extends RuntimeException {
        CalcException(String m) { super(m); }
    }

    interface Node { BigDecimal eval(Map<String, BigDecimal> env); }

    enum T { NUM, VAR, FUNC, PLUS, MINUS, MUL, DIV, MOD, IDIV, POW, FACT, LP, RP, COMMA, EQ, TO,
             SUM, PROD, SQRT, CBRT, EOF }

    static class Tok {
        final T t; final String s;
        Tok(T t, String s) { this.t = t; this.s = s; }
    }

    // ---------------------------------------------------------------- Tokenizer
    static final String[] WORDS = {"floor", "ceil", "sqrt", "cbrt", "prod", "log", "sum", "ln", "mod", "div", "to"};

    static boolean isAscii(char c) { return (c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z'); }

    static List<Tok> lex(String in) {
        List<Tok> out = new ArrayList<>();
        int i = 0, n = in.length();
        while (i < n) {
            char c = in.charAt(i);
            if (Character.isWhitespace(c)) { i++; continue; }

            if ((c >= '0' && c <= '9') || c == '.') {
                int j = i; boolean dot = false;
                while (j < n && ((in.charAt(j) >= '0' && in.charAt(j) <= '9') || in.charAt(j) == '.')) {
                    if (in.charAt(j) == '.') {
                        if (dot) throw new CalcException("Invalid number format");
                        dot = true;
                    }
                    j++;
                }
                String num = in.substring(i, j);
                if (num.equals(".")) throw new CalcException("Invalid number format");
                out.add(new Tok(T.NUM, num));
                i = j; continue;
            }

            if (isAscii(c)) {
                int j = i;
                while (j < n && isAscii(in.charAt(j))) j++;
                String run = in.substring(i, j);
                int p = 0;
                while (p < run.length()) {
                    String w = null;
                    for (String cand : WORDS) if (run.startsWith(cand, p)) { w = cand; break; }
                    if (w == null) { out.add(new Tok(T.VAR, String.valueOf(run.charAt(p)))); p++; continue; }
                    p += w.length();
                    switch (w) {
                        case "sum":  out.add(new Tok(T.SUM, w)); break;
                        case "prod": out.add(new Tok(T.PROD, w)); break;
                        case "sqrt": out.add(new Tok(T.SQRT, w)); break;
                        case "cbrt": out.add(new Tok(T.CBRT, w)); break;
                        case "to":   out.add(new Tok(T.TO, w)); break;
                        case "mod":  out.add(new Tok(T.MOD, w)); break;
                        case "div":  out.add(new Tok(T.IDIV, w)); break;
                        case "log":
                            if (p == run.length() && j < n && in.charAt(j) == '2') {
                                out.add(new Tok(T.FUNC, "log2")); j++;
                            } else out.add(new Tok(T.FUNC, "log"));
                            break;
                        default: out.add(new Tok(T.FUNC, w));
                    }
                }
                i = j; continue;
            }

            switch (c) {
                case '+': out.add(new Tok(T.PLUS, "+")); break;
                case '-': case '\u2212': out.add(new Tok(T.MINUS, "-")); break;
                case '*': case '\u00D7': case '\u00B7': out.add(new Tok(T.MUL, "*")); break;
                case '/':
                    if (i + 1 < n && in.charAt(i + 1) == '/') { out.add(new Tok(T.IDIV, "//")); i++; }
                    else out.add(new Tok(T.DIV, "/"));
                    break;
                case '\u00F7': out.add(new Tok(T.DIV, "/")); break;
                case '%': out.add(new Tok(T.MOD, "%")); break;
                case '^': out.add(new Tok(T.POW, "^")); break;
                case '!': out.add(new Tok(T.FACT, "!")); break;
                case '(': out.add(new Tok(T.LP, "(")); break;
                case ')': out.add(new Tok(T.RP, ")")); break;
                case ',': case ';': out.add(new Tok(T.COMMA, ",")); break;
                case '=': out.add(new Tok(T.EQ, "=")); break;
                case '\u03A3': out.add(new Tok(T.SUM, "sum")); break;
                case '\u03A0': out.add(new Tok(T.PROD, "prod")); break;
                case '\u221A': out.add(new Tok(T.SQRT, "sqrt")); break;
                case '\u221B': out.add(new Tok(T.CBRT, "cbrt")); break;
                default: throw new CalcException("Invalid character in formula");
            }
            i++;
        }
        out.add(new Tok(T.EOF, "end of formula"));
        return out;
    }

    // ------------------------------------------------------------------- Parser
    static class Parser {
        final List<Tok> tk; int p = 0;
        Parser(List<Tok> tk) { this.tk = tk; }

        Tok peek() { return tk.get(p); }
        boolean is(T t) { return peek().t == t; }
        void expect(T t, String what) {
            if (!is(t)) throw new CalcException("Expected " + what + " but found '" + peek().s + "'");
            p++;
        }
        static boolean startsPrimary(T t) {
            return t == T.NUM || t == T.VAR || t == T.FUNC || t == T.LP || t == T.SQRT
                || t == T.CBRT || t == T.SUM || t == T.PROD;
        }

        Node parseAll() {
            Node n = expr();
            if (!is(T.EOF)) throw new CalcException("Unexpected '" + peek().s + "'");
            return n;
        }

        Node expr() {
            Node l = term();
            while (is(T.PLUS) || is(T.MINUS)) {
                boolean add = tk.get(p++).t == T.PLUS;
                final Node a = l, b = term();
                l = add ? env -> a.eval(env).add(b.eval(env))
                        : env -> a.eval(env).subtract(b.eval(env));
            }
            return l;
        }

        Node term() {
            Node l = unary();
            while (true) {
                T t = peek().t;
                final Node a = l;
                if (t == T.MUL)       { p++; final Node b = unary(); l = env -> a.eval(env).multiply(b.eval(env)); }
                else if (t == T.DIV)  { p++; final Node b = unary(); l = env -> div(a.eval(env), b.eval(env)); }
                else if (t == T.MOD)  { p++; final Node b = unary(); l = env -> mod(a.eval(env), b.eval(env)); }
                else if (t == T.IDIV) { p++; final Node b = unary(); l = env -> idiv(a.eval(env), b.eval(env)); }
                else if (startsPrimary(t)) { final Node b = power(); l = env -> a.eval(env).multiply(b.eval(env)); } // implicit: xy, 2x, Cx
                else break;
            }
            return l;
        }

        Node unary() {
            if (is(T.MINUS)) { p++; final Node a = unary(); return env -> a.eval(env).negate(); }
            if (is(T.PLUS))  { p++; return unary(); }
            return power();
        }

        Node power() {
            final Node b = postfix();
            if (is(T.POW)) { p++; final Node x = unary(); return env -> pow(b.eval(env), x.eval(env)); }
            return b;
        }

        Node postfix() {
            Node n = primary();
            while (is(T.FACT)) { p++; final Node a = n; n = env -> fact(a.eval(env)); }
            return n;
        }

        Node primary() {
            Tok t = peek();
            switch (t.t) {
                case NUM: { p++; final BigDecimal v = new BigDecimal(t.s); return env -> v; }
                case VAR: {
                    p++; final String name = t.s;
                    return env -> {
                        BigDecimal v = env.get(name);
                        if (v == null) throw new CalcException("Variable '" + name + "' has no value. Assign it first.");
                        return v;
                    };
                }
                case LP: { p++; Node n = expr(); expect(T.RP, "')'"); return n; }
                case SQRT: { p++; final Node a = primary(); return env -> sqrt(a.eval(env)); }
                case CBRT: { p++; final Node a = primary(); return env -> cbrt(a.eval(env)); }
                case FUNC: {
                    p++; final String name = t.s; final Node a;
                    if (is(T.LP)) { p++; a = expr(); expect(T.RP, "')'"); } else a = primary();
                    return env -> func(name, a.eval(env));
                }
                case SUM:  return loop(true);
                case PROD: return loop(false);
                default:
                    throw new CalcException(t.t == T.EOF ? "Incomplete formula" : "Unexpected '" + t.s + "'");
            }
        }

        Node loop(boolean sum) {
            T self = sum ? T.SUM : T.PROD;
            p++;
            boolean dbl = false;
            if (is(self)) { p++; dbl = true; }
            expect(T.LP, "'('");
            final List<String> vars = new ArrayList<>();
            final List<Node> lo = new ArrayList<>(), hi = new ArrayList<>();
            do {
                Tok v = tk.get(p++);
                if (v.t != T.VAR) throw new CalcException("Expected a loop variable such as i");
                expect(T.EQ, "'='");
                lo.add(expr());
                expect(T.TO, "'to'");
                hi.add(expr());
                vars.add(v.s);
                if (is(T.COMMA)) p++; else break;
            } while (true);
            expect(T.RP, "')'");
            String sym = sum ? "sum" : "prod";
            if (dbl && vars.size() != 2) throw new CalcException("Double " + sym + " needs two ranges: (i=1 to 3, j=1 to 3)");
            if (!dbl && vars.size() != 1) throw new CalcException("Use the double symbol for two ranges");
            final Node body = term();
            return env -> run(sum, vars, lo, hi, 0, body, env, new long[]{0});
        }
    }

    static BigDecimal run(boolean sum, List<String> vars, List<Node> lo, List<Node> hi, int k,
                          Node body, Map<String, BigDecimal> env, long[] counter) {
        if (k == vars.size()) return body.eval(env);
        BigInteger a = toInt(lo.get(k).eval(env), "Lower bound");
        BigInteger b = toInt(hi.get(k).eval(env), "Upper bound");
        String v = vars.get(k);
        BigDecimal old = env.get(v);
        BigDecimal acc = sum ? BigDecimal.ZERO : BigDecimal.ONE;
        try {
            for (BigInteger i = a; i.compareTo(b) <= 0; i = i.add(BigInteger.ONE)) {
                if (++counter[0] > MAX_ITERATIONS) throw new CalcException("Range is too large to compute");
                env.put(v, new BigDecimal(i));
                BigDecimal val = run(sum, vars, lo, hi, k + 1, body, env, counter);
                acc = sum ? acc.add(val) : acc.multiply(val);
            }
        } finally {
            if (old == null) env.remove(v); else env.put(v, old);
        }
        return acc;
    }

    // ----------------------------------------------------------- Math algorithms
    static BigInteger toInt(BigDecimal x, String what) {
        if (x.stripTrailingZeros().scale() > 0) throw new CalcException(what + " must be a whole number");
        return x.toBigIntegerExact();
    }

    static BigDecimal div(BigDecimal a, BigDecimal b) {
        if (b.signum() == 0) throw new CalcException("Division by zero");
        try { return a.divide(b); }                    // exact when possible (keeps big integers intact)
        catch (ArithmeticException ex) { return a.divide(b, MC); }
    }

    static BigDecimal idiv(BigDecimal a, BigDecimal b) {
        if (b.signum() == 0) throw new CalcException("Integer division by zero");
        return a.divide(b, 0, RoundingMode.FLOOR);
    }

    static BigDecimal mod(BigDecimal a, BigDecimal b) {
        if (b.signum() == 0) throw new CalcException("Modulo by zero");
        return a.subtract(idiv(a, b).multiply(b));     // a = (a div b)*b + (a mod b), floor-based
    }

    static BigDecimal pow(BigDecimal b, BigDecimal x) {
        if (x.stripTrailingZeros().scale() <= 0 && x.abs().compareTo(BigDecimal.valueOf(MAX_EXPONENT)) <= 0) {
            int n = x.intValueExact();
            if (n >= 0) return b.pow(n);
            if (b.signum() == 0) throw new CalcException("Zero cannot be raised to a negative power");
            return div(BigDecimal.ONE, b.pow(-n));
        }
        if (b.signum() < 0) throw new CalcException("Negative base with a fractional exponent is not a real number");
        if (b.signum() == 0) {
            if (x.signum() > 0) return BigDecimal.ZERO;
            throw new CalcException("Zero cannot be raised to a negative power");
        }
        return fromDouble(Math.pow(b.doubleValue(), x.doubleValue()));
    }

    static BigDecimal fact(BigDecimal x) {
        BigInteger n = toInt(x, "Factorial input");
        if (n.signum() < 0) throw new CalcException("Factorial is not defined for negative numbers");
        if (n.compareTo(BigInteger.valueOf(MAX_FACTORIAL)) > 0) throw new CalcException("Factorial input too large (max " + MAX_FACTORIAL + ")");
        BigInteger r = BigInteger.ONE;
        for (int i = 2; i <= n.intValue(); i++) r = r.multiply(BigInteger.valueOf(i));
        return new BigDecimal(r);
    }

    static BigDecimal sqrt(BigDecimal x) {
        if (x.signum() < 0) throw new CalcException("Square root of a negative number");
        return x.sqrt(MC);
    }

    static BigDecimal cbrt(BigDecimal x) {
        double d = x.doubleValue();
        if (Double.isInfinite(d)) throw new CalcException("Number too large for cube root");
        BigDecimal r = BigDecimal.valueOf(Math.cbrt(d));
        BigDecimal rounded = r.setScale(0, RoundingMode.HALF_UP);
        if (rounded.pow(3).compareTo(x) == 0) return rounded;      // exact perfect cubes
        return r;
    }

    static BigDecimal log10(BigDecimal x) {
        if (x.signum() <= 0) throw new CalcException("Logarithm is only defined for positive numbers");
        BigDecimal s = x.stripTrailingZeros();
        if (s.unscaledValue().equals(BigInteger.ONE)) return BigDecimal.valueOf(-s.scale()); // exact powers of 10
        return fromDouble(Math.log10(x.doubleValue()));
    }

    static BigDecimal log2(BigDecimal x) {
        if (x.signum() <= 0) throw new CalcException("Logarithm is only defined for positive numbers");
        if (x.stripTrailingZeros().scale() <= 0) {
            BigInteger n = x.toBigIntegerExact();
            if (n.bitCount() == 1) return BigDecimal.valueOf(n.bitLength() - 1);   // exact powers of 2
        }
        return fromDouble(Math.log(x.doubleValue()) / Math.log(2));
    }

    static BigDecimal func(String name, BigDecimal x) {
        switch (name) {
            case "floor": return x.setScale(0, RoundingMode.FLOOR);
            case "ceil":  return x.setScale(0, RoundingMode.CEILING);
            case "log":   return log10(x);
            case "log2":  return log2(x);
            case "ln":
                if (x.signum() <= 0) throw new CalcException("Logarithm is only defined for positive numbers");
                return fromDouble(Math.log(x.doubleValue()));
            default: throw new CalcException("Unknown function '" + name + "'");
        }
    }

    static BigDecimal fromDouble(double d) {
        if (Double.isNaN(d) || Double.isInfinite(d)) throw new CalcException("Result is too large or undefined");
        return BigDecimal.valueOf(d);
    }

    // --------------------------------------------------------------- Output
    static String format(BigDecimal v) {
        v = v.stripTrailingZeros();
        if (v.signum() == 0) return "0";
        if (v.scale() <= 0) return v.toBigInteger().toString();
        v = v.round(new MathContext(15, RoundingMode.HALF_UP)).stripTrailingZeros();
        return v.scale() <= 0 ? v.toBigInteger().toString() : v.toPlainString();
    }

    static String json(boolean ok, String key, String val) {
        StringBuilder sb = new StringBuilder("{\"ok\":" + ok + ",\"" + key + "\":\"");
        for (char c : val.toCharArray()) {
            if (c == '"' || c == '\\') sb.append('\\').append(c);
            else if (c < 32 || c > 126) sb.append(String.format("\\u%04x", (int) c));
            else sb.append(c);
        }
        return sb.append("\"}").toString();
    }

    public static void main(String[] args) throws Exception {
        BufferedReader br = new BufferedReader(new InputStreamReader(System.in, StandardCharsets.UTF_8));
        String expr = br.readLine();
        try {
            if (expr == null || expr.trim().isEmpty()) throw new CalcException("Please enter a formula");
            if (expr.length() > 500) throw new CalcException("Formula is too long");
            Map<String, BigDecimal> env = new HashMap<>();
            String line;
            while ((line = br.readLine()) != null) {
                if (line.length() < 3 || line.charAt(1) != '=' || !isAscii(line.charAt(0))) continue;
                try { env.put(line.substring(0, 1), new BigDecimal(line.substring(2).trim())); }
                catch (NumberFormatException ex) { throw new CalcException("Invalid value for variable " + line.charAt(0)); }
            }
            Node tree = new Parser(lex(expr)).parseAll();
            System.out.println(json(true, "result", format(tree.eval(env))));
        } catch (CalcException ex) {
            System.out.println(json(false, "error", ex.getMessage()));
        } catch (ArithmeticException ex) {
            System.out.println(json(false, "error", "Math error: " + ex.getMessage()));
        } catch (StackOverflowError ex) {
            System.out.println(json(false, "error", "Formula is nested too deeply"));
        } catch (Exception ex) {
            System.out.println(json(false, "error", "Unexpected error"));
        }
    }
}
