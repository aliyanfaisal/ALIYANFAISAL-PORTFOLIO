<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Strict content filter for public comment text: blocks scripts and markup, code/SQL
 * injection payloads, links (including obfuscated ones), contact details, abusive
 * language and common spam patterns.
 */
class SafeComment implements ValidationRule
{
    /**
     * @var array<string, string>
     */
    protected array $patterns = [
        'a script or markup tag' => '/<\s*[a-z!\/?][^>]*>?|<\s*\/|&lt;\s*[a-z\/!]/i',
        'executable code' => '/(javascript\s*:|vbscript\s*:|data\s*:\s*text\/html|\bon[a-z]{3,}\s*=|<\?php|\beval\s*\(|\balert\s*\(|\bprompt\s*\(|\bconfirm\s*\(|document\s*\.\s*(cookie|write|location)|window\s*\.\s*location|\bfetch\s*\(|\bXMLHttpRequest\b|String\s*\.\s*fromCharCode|\batob\s*\(|\bexec\s*\(|\bsystem\s*\(|\bshell_exec\b|\bbase64_decode\b|\{\{.*\}\}|\{%.*%\}|\$\{.*\})/i',
        'a database or shell command' => '/(\bunion\s+(all\s+)?select\b|\bselect\s+(?:\*|[\w,`.\s]+?)\s+from\s+[\w.`]+\s*(?:where|limit|order|group|;|--)|\bdrop\s+(table|database)\b|\binsert\s+into\b|\bdelete\s+from\b|\bupdate\s+[\w.`]+\s+set\s+[\w`]+\s*=|\bor\s+1\s*=\s*1\b|\'\s*or\s*\'|;\s*--|\/\*.*\*\/|\bxp_cmdshell\b|\brm\s+-rf\b|\bwget\s|\bcurl\s+-|\bchmod\s+[0-7]{3}|\/etc\/passwd|\.\.\/\.\.\/|\bsleep\s*\(\s*\d+\s*\))/i',
        'a link' => '/(https?\s*:?\s*\/\/|hxxps?|ftp:\/\/|www\s*\.|\bt\.me\/|\bbit\.ly\b|\btinyurl\b|\b[a-z0-9-]+\s*(\.|\[\.\]|\(\.\)|\s+dot\s+)\s*(com|net|org|io|co|info|biz|me|dev|xyz|online|shop|app|ai|us|uk|ca|ru|cn|top|site|club|link|click|store|live|pro|vip|win|bet|casino|tk|ml|ga|cf|gq)\b)/i',
        'an email address' => '/[^\s@]+\s*(@|\[at\]|\(at\)|\s+at\s+)\s*[^\s@]+\s*(\.|\[dot\]|\(dot\)|\s+dot\s+)\s*[a-z]{2,}/i',
        'a phone number' => '/(\+?\d[\d\-.\s()]{6,}\d)/',
        'a messaging handle' => '/\b(whats\s*app|telegram|wechat|skype|snap\s*chat|signal)\b/i',
    ];

    /**
     * Spam phrases and topics.
     *
     * @var array<int, string>
     */
    protected array $spamPhrases = [
        'viagra', 'cialis', 'casino', 'poker', 'betting', 'sportsbook', 'payday loan', 'cheap loan',
        'forex', 'crypto giveaway', 'bitcoin doubler', 'earn money fast', 'make money online',
        'work from home', 'click here', 'click below', 'buy now', 'order now', 'limited offer',
        'act now', 'free gift', 'you have won', 'you won', 'congratulations you',
        'seo services', 'seo package', 'backlinks', 'guest post', 'rank your website', 'traffic to your site',
        'weight loss', 'diet pill', 'enlargement', 'escort', 'dating site', 'hot singles', 'adult site',
        'porn', 'xxx', 'onlyfans', 'nude', 'replica watch', 'fake id', 'buy followers', 'cheap pills',
        'online pharmacy', 'no prescription', 'miracle cure', '100% free', 'risk free', 'double your',
        'investment opportunity', 'guaranteed income', 'mlm', 'get rich', 'subscribe to my',
        'check out my', 'visit my', 'follow me on', 'dm me', 'contact me at', 'promo code', 'discount code',
    ];

    /**
     * Abusive and offensive words, matched on word boundaries after normalising
     * obfuscation (leetspeak, separators, repeated letters).
     *
     * @var array<int, string>
     */
    protected array $abusiveWords = [
        'fuck', 'fucker', 'fucking', 'motherfucker', 'shit', 'bullshit', 'bitch', 'bastard', 'asshole',
        'arsehole', 'dick', 'dickhead', 'cock', 'pussy', 'cunt', 'whore', 'slut', 'hoe', 'twat', 'wanker',
        'prick', 'douche', 'douchebag', 'jackass', 'dumbass', 'retard', 'retarded', 'moron', 'idiot',
        'imbecile', 'stupid', 'loser', 'scumbag', 'piss', 'crap', 'damn', 'nigger', 'nigga',
        'faggot', 'fag', 'dyke', 'tranny', 'chink', 'spic', 'kike', 'paki', 'coon', 'wetback',
        'rape', 'rapist', 'molest', 'pedophile', 'paedophile', 'kill yourself', 'kys', 'die in a fire',
        'go die', 'hang yourself', 'terrorist', 'nazi', 'hitler', 'stfu', 'gtfo', 'wtf', 'bloody hell',
        'kutta', 'kutti', 'harami', 'haramzada', 'bhenchod', 'behenchod', 'madarchod', 'chutiya', 'chutia',
        'gandu', 'randi', 'bhosdi', 'bhosdike', 'gaand', 'saala', 'kamina', 'kameena', 'suar',
    ];

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $text = $this->normalise($value);

        if ($this->hasHiddenCharacters($value)) {
            $fail('The :attribute contains invalid characters.');

            return;
        }

        foreach ($this->patterns as $label => $pattern) {
            if (preg_match($pattern, $value) === 1 || preg_match($pattern, $text) === 1) {
                $fail("The :attribute must not contain {$label}.");

                return;
            }
        }

        if ($this->containsAbuse($text)) {
            $fail('The :attribute contains language that is not allowed. Please keep the discussion respectful.');

            return;
        }

        if ($this->containsSpamPhrase($text)) {
            $fail('The :attribute looks like spam or promotion and cannot be posted.');

            return;
        }

        if ($reason = $this->spamHeuristic($value)) {
            $fail("The :attribute looks like spam ({$reason}) and cannot be posted.");
        }
    }

    /**
     * Zero-width, control and bidirectional-override characters used to smuggle or disguise content.
     */
    protected function hasHiddenCharacters(string $value): bool
    {
        if (! mb_check_encoding($value, 'UTF-8')) {
            return true;
        }

        return preg_match('/[\x{0000}-\x{0008}\x{000B}\x{000C}\x{000E}-\x{001F}\x{007F}\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{2064}\x{FEFF}]/u', $value) === 1;
    }

    /**
     * Lower-case, strip accents and collapse leetspeak so "f.u.c.k" / "sh1t" / "fuuuck" are caught.
     */
    protected function normalise(string $value): string
    {
        $text = mb_strtolower(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = strtr($text, ['@' => 'a', '$' => 's', '0' => 'o', '1' => 'i', '3' => 'e', '4' => 'a', '5' => 's', '7' => 't', '!' => 'i']);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return $ascii === false ? $text : $ascii;
    }

    protected function containsAbuse(string $text): bool
    {
        // Spaced/dotted single letters ("f u c k", "f.u.c.k") are joined before matching.
        $joined = preg_replace_callback('/\b(?:[a-z][\s.\-_*]+){2,}[a-z]\b/', fn ($m) => preg_replace('/[\s.\-_*]+/', '', $m[0]), $text);
        // Collapse 3+ repeated letters down to one ("fuuuck" => "fuck"), and 2 to a single/double form.
        $collapsed = preg_replace('/(.)\1{2,}/', '$1', $joined);
        $doubled = preg_replace('/(.)\1+/', '$1', $joined);

        foreach ([$joined, $collapsed, $doubled] as $candidate) {
            foreach ($this->abusiveWords as $word) {
                $needle = $candidate === $doubled ? preg_replace('/(.)\1+/', '$1', $word) : $word;

                if (preg_match('/(?<![a-z])'.preg_quote($needle, '/').'(?:s|es|ed|er|ing)?(?![a-z])/', $candidate) === 1) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function containsSpamPhrase(string $text): bool
    {
        $flat = preg_replace('/[^a-z0-9%]+/', ' ', $text);

        foreach ($this->spamPhrases as $phrase) {
            if (preg_match('/(?<![a-z0-9])'.preg_quote($phrase, '/').'(?![a-z0-9])/', $flat) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Structural spam signals: shouting, repeated characters/words, symbol floods, gibberish.
     */
    protected function spamHeuristic(string $value): ?string
    {
        $trimmed = trim($value);
        $letters = preg_replace('/[^\p{L}]/u', '', $trimmed);
        $length = mb_strlen($letters);

        if ($length >= 12 && mb_strlen(preg_replace('/[^\p{Lu}]/u', '', $letters)) / $length > 0.7) {
            return 'excessive capital letters';
        }

        if (preg_match('/(.)\1{6,}/su', $trimmed) === 1) {
            return 'repeated characters';
        }

        if (preg_match('/(\b.{3,}?\b)(?:\W+\1){2,}/siu', $trimmed) === 1) {
            return 'repeated text';
        }

        $words = preg_split('/\s+/u', mb_strtolower($trimmed), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($words) >= 8 && count(array_unique($words)) / count($words) < 0.4) {
            return 'repeated words';
        }

        $symbols = mb_strlen(preg_replace('/[\p{L}\p{N}\s.,\'’"?!()\-:;]/u', '', $trimmed));
        if (mb_strlen($trimmed) >= 10 && $symbols / mb_strlen($trimmed) > 0.25) {
            return 'too many symbols';
        }

        if (preg_match('/[!?]{4,}/', $trimmed) === 1) {
            return 'excessive punctuation';
        }

        if (preg_match('/\p{Extended_Pictographic}{5,}/u', $trimmed) === 1) {
            return 'too many emojis';
        }

        if ($length >= 15 && preg_match('/\p{L}{25,}/u', $trimmed) === 1) {
            return 'unreadable text';
        }

        if ($length >= 8 && preg_match('/[^aeiouy\W\d_]{7,}/iu', $trimmed) === 1 && ! str_contains($trimmed, ' ')) {
            return 'unreadable text';
        }

        return null;
    }
}
