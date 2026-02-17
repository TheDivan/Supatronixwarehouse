<#
PowerShell helper to apply the production patch bundle (Option C).
Usage:
- powershell -ExecutionPolicy Bypass -File scripts/apply-prod-patch.ps1 -Branch prod-prod-final
- powershell -ExecutionPolicy Bypass -File scripts/apply-prod-patch.ps1 -Branch prod-prod-final -DryRun
Notes:
- Requires git to be installed and accessible in PATH.
- Patch ZIP supatronix-prod-final.zip must exist in repo root.
#>
param(
  [string]$Branch = "",
  [switch]$DryRun
)

$zipPath = Join-Path -Path (Get-Location) -ChildPath "supatronix-prod-final.zip"
if (-Not (Test-Path $zipPath)) {
  Write-Error "Patch ZIP not found at $zipPath. Run tool to generate it first (tools/make-prod-zip.sh for Bash; this script expects the PATCH ZIP)."
  exit 1
}

$root = (Get-Location).Path
$tempRoot = Join-Path -Path ([System.IO.Path]::GetTempPath()) -ChildPath ([Guid]::NewGuid().ToString())

Write-Host "[prod-patch-ps1] Extracting patch to $tempRoot"
Expand-Archive -Path $zipPath -DestinationPath $tempRoot -Force

if ($Branch -and $Branch.Trim() -ne "") {
  Write-Host "[prod-patch-ps1] Creating/Switching to branch '$Branch'"
  git checkout -B $Branch
}

Write-Host "[prod-patch-ps1] Applying patch content to repository root '$root'..."

$patchRoot = $tempRoot
Get-ChildItem -Path $patchRoot -Recurse -Force | ForEach-Object {
  if ($_.PSIsContainer) {
    $rel = $_.FullName.Substring($patchRoot.Length).TrimStart('\')
    $destDir = Join-Path $root $rel
    if (-Not (Test-Path $destDir)) {
      New-Item -ItemType Directory -Force -Path $destDir | Out-Null
    }
  } else {
    $rel = $_.FullName.Substring($patchRoot.Length).TrimStart('\')
    $dest = Join-Path $root $rel
    $destDir = Split-Path $dest -Parent
    if (-Not (Test-Path $destDir)) {
      New-Item -ItemType Directory -Force -Path $destDir | Out-Null
    }
    Copy-Item -Path $_.FullName -Destination $dest -Force
  }
}

$changes = git status --porcelain
if ($DryRun) {
  Write-Host "[prod-patch-ps1] DRY RUN: patch would change the following files (if any):"
  $changes
  Remove-Item -Recurse -Force $tempRoot -ErrorAction SilentlyContinue
  exit 0
}

if (-Not $changes) {
  Write-Host "[prod-patch-ps1] No changes detected after patch application."
  Remove-Item -Recurse -Force $tempRoot -ErrorAction SilentlyContinue
  exit 0
}

Write-Host "[prod-patch-ps1] Staging changes..."
git add -A
git commit -m "prod(final): apply Supatronix MVP patch (RBAC runtime, Phase 2, Not-Repairable PDF, seeds, CI/CD, Docker Compose)"
Write-Host "[prod-patch-ps1] Patch applied on branch: $(git rev-parse --abbrev-ref HEAD)"

Remove-Item -Recurse -Force $tempRoot -ErrorAction SilentlyContinue
Write-Host "[prod-patch-ps1] Done. Remember to push the branch and open a PR."
