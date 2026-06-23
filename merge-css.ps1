$theme = 'c:\Users\admin\Desktop\anna-baylis-live'
$out   = $theme + '\assets\css\global.css'

$files = @(
  'assets/css/variables.css',
  'assets/css/reset.css',
  'assets/css/base.css',
  'assets/css/layout.css',
  'assets/css/utilities.css',
  'assets/css/animations.css',
  'assets/css/components/buttons.css',
  'assets/css/components/cards.css',
  'assets/css/components/badges.css',
  'assets/css/components/navigation.css',
  'assets/css/components/forms.css',
  'assets/css/components/testimonials.css',
  'assets/css/components/media.css',
  'assets/css/sections/header.css',
  'assets/css/sections/footer.css'
)

$content = "/**`r`n * Global Styles - Anna Baylis Theme`r`n * Single merged bundle loaded on every page.`r`n * Contains: variables, reset, base, layout, utilities,`r`n * animations, components (buttons, cards, badges,`r`n * navigation, forms, testimonials, media),`r`n * sections (header, footer).`r`n * Google Fonts are enqueued separately via wp_enqueue_style.`r`n * @package Anna_Baylis`r`n * @since   1.0.0`r`n */`r`n"

foreach ($rel in $files) {
  $path = $theme + '\' + ($rel -replace '/', '\')
  if (Test-Path $path) {
    $src = [System.IO.File]::ReadAllText($path, [System.Text.Encoding]::UTF8)
    if ($rel -eq 'assets/css/variables.css') {
      $src = $src -replace "@import url\('https://fonts\.googleapis\.com[^']*'\);\s*`r?`n", ''
    }
    $label = $rel -replace 'assets/css/', '' -replace '\.css', ''
    $content += "`r`n/* ============================================================`r`n   " + $label.ToUpper() + "`r`n   ============================================================ */`r`n" + $src + "`r`n"
  } else {
    Write-Warning "NOT FOUND: $path"
  }
}

[System.IO.File]::WriteAllText($out, $content, [System.Text.Encoding]::UTF8)
$kb = [Math]::Round((Get-Item $out).Length / 1KB, 1)
Write-Host "global.css written: $kb KB"
