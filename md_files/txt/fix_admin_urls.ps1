# PowerShell script to fix all hardcoded /admin URLs in views
# This script replaces hardcoded URLs with app_base_url() helper function calls

$viewsPath = "app\Views\admin"
$files = Get-ChildItem -Path $viewsPath -Recurse -Filter *.php

$replacements = @{
    # href patterns
    'href="/admin'  = 'href="<?php echo app_base_url(''/admin'
    'href=''/admin' = 'href=''<?php echo app_base_url(''/admin'
    
    # action patterns  
    'action="/admin'  = 'action="<?php echo app_base_url(''/admin'
    'action=''/admin' = 'action=''<?php echo app_base_url(''/admin'
    
    # window.location patterns
    'window.location.href = ''/admin' = 'window.location.href = ''<?php echo app_base_url(''/admin'
    'window.location.href = "/admin'  = 'window.location.href = "<?php echo app_base_url("/admin'
    'window.location.href=''/admin'   = 'window.location.href=''<?php echo app_base_url(''/admin'
    'window.location.href="/admin'    = 'window.location.href="<?php echo app_base_url("/admin'
    'window.location = ''/admin'      = 'window.location = ''<?php echo app_base_url(''/admin'
    'window.location = "/admin'       = 'window.location = "<?php echo app_base_url("/admin'
}

$totalChanges = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    $fileChanges = 0
    
    foreach ($pattern in $replacements.Keys) {
        $replacement = $replacements[$pattern]
        if ($content -match [regex]::Escape($pattern)) {
            $content = $content -replace [regex]::Escape($pattern), $replacement
            $fileChanges++
        }
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $totalChanges += $fileChanges
        Write-Host "Fixed $fileChanges patterns in: $($file.FullName)"
    }
}

Write-Host "`nTotal files processed: $($files.Count)"
Write-Host "Total changes made: $totalChanges"
