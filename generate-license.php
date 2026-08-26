<?php

/**
 * ============================================================================
 * Egypt Technology Group
 * Vendor License Generator CLI
 * ============================================================================
 *
 * PURPOSE
 * -------
 * Generates cryptographically signed subscription entitlement licenses
 * for clients of the University Academic Portal.
 *
 * IMPORTANT
 * ---------
 * This script is a VENDOR-ONLY tool. The vendor private signing key must
 * never be deployed to or exposed within any client installation.
 *
 * ----------------------------------------------------------------------------
 * USAGE
 * ----------------------------------------------------------------------------
 *
 * Interactive mode:
 *   php generate-license.php
 *
 * Show help:
 *   php generate-license.php --help
 *
 * License all modules for 1 year:
 *   php generate-license.php --all --tier=enterprise --days=365
 *
 * License specific modules:
 *   php generate-license.php \
 *     --client-id=client_01 \
 *     --client-name="Mansoura University" \
 *     --tier=standard \
 *     --modules=academic-structure,admissions,academic-services,cms \
 *     --days=730 \
 *     --output=university_license.json
 *
 * ----------------------------------------------------------------------------
 * OPTIONS
 * ----------------------------------------------------------------------------
 *
 * --client-id
 *   Unique client identifier (slug).
 *   Default: egyitech_portal_YYYY
 *
 * --client-name
 *   Client or organization name.
 *   Default: Egypt Technology Group Client Portal
 *
 * --tier
 *   Subscription tier.
 *   Allowed: starter, standard, enterprise
 *   Default: enterprise
 *
 * --modules
 *   Comma-separated list of module IDs to license.
 *   Example:
 *   --modules=academic-structure,admissions,cms
 *
 * --all
 *   License all available modules.
 *   Default: enabled when --modules is not specified.
 *
 * --days
 *   License validity duration in days.
 *   Default: 365
 *
 * --output
 *   Path where the generated license JSON certificate will be saved.
 *   If omitted, the certificate is printed to the terminal.
 *
 * --help
 *   Display usage information and available options.
 *
 * ----------------------------------------------------------------------------
 * LICENSE FLOW
 * ----------------------------------------------------------------------------
 *
 * 1. Define the client and subscription tier.
 * 2. Select the licensed modules or use --all.
 * 3. Set the license validity period.
 * 4. Generate the signed license certificate.
 * 5. Deliver the certificate to the client.
 * 6. The client application verifies the certificate using the vendor
 *    public key.
 *
 * The vendor private key is used ONLY for signing and must remain under
 * the vendor's secure control.
 *
 * ============================================================================
 */

declare(strict_types=1);

// 1. Locate and autoload backend dependencies
$backendDir = __DIR__ . DIRECTORY_SEPARATOR . 'backend';
$autoloadPath = $backendDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (!file_exists($autoloadPath)) {
    fwrite(STDERR, "\033[31m[ERROR] Composer autoload file not found at: {$autoloadPath}\033[0m\n");
    fwrite(STDERR, "Please run 'composer install' inside the backend directory first.\n");
    exit(1);
}

require_once $autoloadPath;

// Bootstrap Laravel Application for configuration & environment loading
$bootstrapApp = $backendDir . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';
if (file_exists($bootstrapApp)) {
    $app = require_once $bootstrapApp;
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
}

use App\Core\Security\VendorKeyProvider;

// 2. Available Portal Modules
$availableModules = [
    'academic-structure' => 'Academic Structure (Colleges, Departments, Programs)',
    'admissions'         => 'Admissions & Student Applications',
    'academic-services'  => 'Academic Services (Requests, Schedules, Statements)',
    'cms'                => 'CMS (News, Articles, Categories)',
    'events'             => 'Events & Announcements Calendar',
    'documents'          => 'Document Repository & Archive',
    'results'            => 'Student Examination Results & Transcripts',
];

// 3. Parse CLI options or prompt interactively
$options = getopt('', [
    'client-id::',
    'client-name::',
    'tier::',
    'modules::',
    'all',
    'days::',
    'output::',
    'signing-key::',
    'help',
]);

if (isset($options['help'])) {
    echo <<<HELP
\033[1;33mEgypt Technology Group - License Generator CLI\033[0m

Options:
  --client-id=STRING      Unique client slug/ID (default: egyitech_client_portal)
  --client-name=STRING    Human-readable client/university name
  --tier=STRING           Subscription tier: starter | standard | enterprise (default: enterprise)
  --modules=LIST          Comma-separated list of licensed module IDs
  --all                   License all available modules
  --days=NUMBER           Validity period in days from now (default: 365)
  --output=FILE           Path to save the JSON license file
  --signing-key=STRING    Override vendor master signing key
  --help                  Show this help message

Examples:
  php generate-license.php --all --tier=enterprise --days=730 --output=client_license.json
  php generate-license.php --client-name="Mansoura University" --modules=academic-structure,admissions,cms

HELP;
    exit(0);
}

echo "\n\033[1;36m================================================================\033[0m\n";
echo "\033[1;33m  EGYTECH VENDOR CONTROL PLANE - CRYPTOGRAPHIC LICENSE GENERATOR \033[0m\n";
echo "\033[1;36m================================================================\033[0m\n\n";

// Helper for interactive prompts
$prompt = function (string $question, string $default = '') {
    $formatted = "\033[1;32m? {$question}\033[0m " . ($default !== '' ? "[\033[33m{$default}\033[0m]: " : ": ");
    echo $formatted;
    $input = trim((string) fgets(STDIN));
    return $input !== '' ? $input : $default;
};

$isInteractive = empty($options);

// Resolve Client ID & Name
$clientId = $options['client-id'] ?? ($isInteractive ? $prompt('Client ID (slug)', 'egyitech_portal_' . date('Y')) : 'egyitech_portal_' . date('Y'));
$clientName = $options['client-name'] ?? ($isInteractive ? $prompt('Client / University Name', 'Egypt Technology Group Client Portal') : 'Egypt Technology Group Client Portal');

// Resolve Subscription Tier
$tier = $options['tier'] ?? ($isInteractive ? $prompt('Subscription Tier (starter / standard / enterprise)', 'enterprise') : 'enterprise');

// Resolve Licensed Modules
$selectedModules = [];
if (isset($options['all'])) {
    $selectedModules = array_keys($availableModules);
} elseif (!empty($options['modules'])) {
    $rawList = explode(',', (string) $options['modules']);
    $selectedModules = array_values(array_filter(array_map('trim', $rawList)));
} elseif ($isInteractive) {
    echo "\n\033[1;34mAvailable Modules:\033[0m\n";
    $modKeys = array_keys($availableModules);
    foreach ($modKeys as $idx => $modKey) {
        $num = $idx + 1;
        echo "  [{$num}] \033[1;37m{$modKey}\033[0m - {$availableModules[$modKey]}\n";
    }
    echo "  [A] \033[1;33mALL MODULES (Full Suite)\033[0m\n\n";

    $choice = $prompt('Select modules to license (e.g., "1,2,4" or "A" for all)', 'A');
    if (strtoupper($choice) === 'A' || $choice === '') {
        $selectedModules = array_keys($availableModules);
    } else {
        $indices = explode(',', $choice);
        foreach ($indices as $i) {
            $numIdx = (int) trim($i) - 1;
            if (isset($modKeys[$numIdx])) {
                $selectedModules[] = $modKeys[$numIdx];
            }
        }
    }
} else {
    // Default to all if not specified in non-interactive mode
    $selectedModules = array_keys($availableModules);
}

// Resolve Validity Period
$days = (int) ($options['days'] ?? ($isInteractive ? $prompt('License Validity in Days', '365') : '365'));
$validUntil = (new \DateTimeImmutable())->modify("+{$days} days")->format('Y-m-d\TH:i:s\Z');

// Custom signing key override if provided
$customKey = $options['signing-key'] ?? null;

// 4. Construct Payload and Sign Cryptographically
$payload = [
    'client_id' => $clientId,
    'client_name' => $clientName,
    'tier' => strtolower($tier),
    'licensed_modules' => array_values(array_unique($selectedModules)),
    'issued_at' => (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z'),
    'valid_until' => $validUntil,
    'nonce' => bin2hex(random_bytes(16)),
];

$provider = new VendorKeyProvider($customKey);
$signedPackage = $provider->signPayload($payload);

// Verify the package before outputting
$isValid = $provider->verifySignature($signedPackage);
if (!$isValid) {
    fwrite(STDERR, "\033[31m[ERROR] Self-verification failed for generated signature!\033[0m\n");
    exit(1);
}

$jsonOutput = json_encode($signedPackage, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// 5. Output Summary & Certificate
echo "\n\033[1;32m✔ Cryptographic License Package Generated Successfully!\033[0m\n";
echo "----------------------------------------------------------------\n";
echo " \033[1;37mClient ID:\033[0m        {$payload['client_id']}\n";
echo " \033[1;37mClient Name:\033[0m      {$payload['client_name']}\n";
echo " \033[1;37mTier:\033[0m             \033[1;33m" . strtoupper($payload['tier']) . "\033[0m\n";
echo " \033[1;37mValid Until:\033[0m      {$payload['valid_until']} ({$days} days)\n";
echo " \033[1;37mLicensed Modules:\033[0m (" . count($payload['licensed_modules']) . ")\n";
foreach ($payload['licensed_modules'] as $mod) {
    echo "   - \033[32m{$mod}\033[0m\n";
}
echo " \033[1;37mSignature:\033[0m        \033[36m{$signedPackage['signature']}\033[0m\n";
echo " \033[1;37mAlgorithm:\033[0m        {$signedPackage['algorithm']}\n";
echo "----------------------------------------------------------------\n\n";

echo "\033[1;33mSigned JSON License Certificate:\033[0m\n";
echo "\033[37m" . $jsonOutput . "\033[0m\n\n";

// 6. Save to File if requested or prompted
$outputPath = $options['output'] ?? null;
if ($outputPath === null && $isInteractive) {
    $saveChoice = $prompt('Save license to a JSON file? (y/n)', 'y');
    if (strtolower($saveChoice) === 'y') {
        $outputPath = $prompt('File path to save', 'license.json');
    }
}

if ($outputPath) {
    file_put_contents($outputPath, $jsonOutput);
    echo "\033[1;32m✔ License certificate saved to: {$outputPath}\033[0m\n";
}

echo "\n\033[1;36mInstructions to Apply:\033[0m\n";
echo " 1. Navigate to: \033[1;34mhttp://localhost:5173/admin/modules\033[0m in your browser.\n";
echo " 2. Click the \033[1;33m'Apply Vendor License Certificate'\033[0m button in the top banner.\n";
echo " 3. Copy & paste the JSON block above and click \033[1;32m'Verify & Apply License'\033[0m.\n\n";
