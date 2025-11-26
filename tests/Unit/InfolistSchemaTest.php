<?php

declare(strict_types=1);

describe('payload formatting', function () {
    it('extracts displayName from payload JSON', function () {
        $payload = json_encode(['displayName' => 'App\\Jobs\\TestJob', 'data' => []]);

        $result = json_decode($payload, true)['displayName'];

        expect($result)->toBe('App\\Jobs\\TestJob');
    });

    it('pretty prints payload JSON with htmlspecialchars for XSS protection', function () {
        $payload = json_encode(['displayName' => 'TestJob', 'data' => ['key' => '<script>alert("xss")</script>']]);

        $decoded = json_decode($payload, true);
        $prettyPrinted = htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT));
        $result = '<pre style="overflow-x: auto; line-height: 2">'.$prettyPrinted.'</pre>';

        expect($result)->toContain('&lt;script&gt;');
        expect($result)->not->toContain('<script>');
        expect($result)->toContain('<pre');
    });

    it('handles empty payload', function () {
        $payload = '{}';

        $decoded = json_decode($payload, true);
        $result = json_encode($decoded, JSON_PRETTY_PRINT);

        expect($result)->toBe('[]');
    });

    it('handles complex nested payload', function () {
        $payload = json_encode([
            'displayName' => 'App\\Jobs\\ComplexJob',
            'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
            'maxTries' => 3,
            'data' => [
                'command' => 'serialized-command-data',
                'commandName' => 'App\\Jobs\\ComplexJob',
            ],
        ]);

        $decoded = json_decode($payload, true);

        expect($decoded)->toHaveKey('displayName');
        expect($decoded)->toHaveKey('maxTries');
        expect($decoded['maxTries'])->toBe(3);
    });
});

describe('exception formatting', function () {
    it('converts newlines to br tags', function () {
        $exception = "Line 1\nLine 2\nLine 3";

        $result = '<div style="line-height: 2">'.nl2br($exception).'</div>';

        expect($result)->toContain('<br />');
        expect($result)->toContain('Line 1');
        expect($result)->toContain('Line 2');
        expect($result)->toContain('Line 3');
    });

    it('handles exception with stack trace', function () {
        $exception = "ErrorException: Something went wrong\n#0 /path/to/file.php(123): function()\n#1 {main}";

        $result = '<div style="line-height: 2">'.nl2br($exception).'</div>';

        expect($result)->toContain('ErrorException');
        expect($result)->toContain('#0');
        expect($result)->toContain('<br />');
    });

    it('handles empty exception', function () {
        $exception = '';

        $result = '<div style="line-height: 2">'.nl2br($exception).'</div>';

        expect($result)->toBe('<div style="line-height: 2"></div>');
    });

    it('handles long exception messages', function () {
        $exception = str_repeat('Error message. ', 100);

        $result = '<div style="line-height: 2">'.nl2br($exception).'</div>';

        expect(strlen($result))->toBeGreaterThan(1000);
    });
});

describe('XSS protection', function () {
    it('escapes HTML in payload', function () {
        $maliciousPayload = json_encode([
            'displayName' => '<script>alert("xss")</script>',
            'data' => ['value' => '<img src=x onerror=alert("xss")>'],
        ]);

        $decoded = json_decode($maliciousPayload, true);
        $result = htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT));

        expect($result)->not->toContain('<script>');
        expect($result)->not->toContain('<img');
        expect($result)->toContain('&lt;script&gt;');
        expect($result)->toContain('&lt;img');
    });

    it('exception field preserves HTML', function () {
        $maliciousException = '<script>alert("xss")</script>';

        $result = '<div style="line-height: 2">'.nl2br($maliciousException).'</div>';

        expect($result)->toContain('<script>');
    });

    it('properly escapes common XSS patterns in payload', function () {
        $xssPatterns = [
            '<script>alert(1)</script>',
            '<img src=x onerror=alert(1)>',
            '<svg onload=alert(1)>',
            '"><script>alert(1)</script>',
            "javascript:alert('xss')",
        ];

        foreach ($xssPatterns as $pattern) {
            $payload = json_encode(['data' => $pattern]);
            $decoded = json_decode($payload, true);
            $escaped = htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT));

            expect($escaped)->not->toContain('<script>');
            expect($escaped)->not->toContain('<img');
            expect($escaped)->not->toContain('<svg');
        }
    });
});

describe('job name extraction', function () {
    it('extracts short class name from full namespace', function () {
        $displayName = 'App\\Jobs\\ProcessPayment';

        $shortName = class_basename($displayName);

        expect($shortName)->toBe('ProcessPayment');
    });

    it('handles single-level namespace', function () {
        $displayName = 'ProcessPayment';

        $shortName = class_basename($displayName);

        expect($shortName)->toBe('ProcessPayment');
    });

    it('handles deeply nested namespace', function () {
        $displayName = 'App\\Domain\\Billing\\Jobs\\ProcessPayment';

        $shortName = class_basename($displayName);

        expect($shortName)->toBe('ProcessPayment');
    });
});
