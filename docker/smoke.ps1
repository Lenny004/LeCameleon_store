#Requires -Version 5.1
<#
.SYNOPSIS
  HTTP smoke checks against the local Docker storefront (nginx :8080).
.EXAMPLE
  .\docker\smoke.ps1
  .\docker\smoke.ps1 -BaseUrl http://localhost:8080
#>
param(
    [string]$BaseUrl = 'http://localhost:8080',
    [string]$CustomerEmail = 'customer@lecameleon.store',
    [string]$CustomerPassword = 'password'
)

$ErrorActionPreference = 'Stop'
$BaseUrl = $BaseUrl.TrimEnd('/')
$failed = 0
$passed = 0

function Write-Result {
    param(
        [string]$Name,
        [int]$Expected,
        [int]$Actual,
        [string]$Detail = ''
    )

    $suffix = ''
    if ($Detail) {
        $suffix = " ($Detail)"
    }

    if ($Actual -eq $Expected) {
        $script:passed++
        Write-Host ("  PASS  {0} -> {1}{2}" -f $Name, $Actual, $suffix) -ForegroundColor Green
    } else {
        $script:failed++
        Write-Host ("  FAIL  {0} -> got {1}, expected {2}{3}" -f $Name, $Actual, $Expected, $suffix) -ForegroundColor Red
    }
}

function Get-StatusCode {
    param(
        [string]$Url,
        [Microsoft.PowerShell.Commands.WebRequestSession]$Session = $null,
        [switch]$AllowRedirect
    )

    try {
        $params = @{
            Uri                = $Url
            Method             = 'GET'
            MaximumRedirection = $(if ($AllowRedirect) { 5 } else { 0 })
            TimeoutSec         = 20
            UseBasicParsing    = $true
        }
        if ($Session) {
            $params.WebSession = $Session
        }

        $response = Invoke-WebRequest @params
        return [int]$response.StatusCode
    } catch {
        $resp = $_.Exception.Response
        if ($null -ne $resp) {
            try {
                return [int]$resp.StatusCode.value__
            } catch {
                try {
                    return [int]$resp.StatusCode
                } catch {
                    # fall through
                }
            }
        }
        throw
    }
}

function Get-CsrfToken {
    param(
        [string]$Html
    )

    if ($Html -match 'name="_token"\s+value="([^"]+)"') {
        return $Matches[1]
    }
    if ($Html -match 'name="csrf-token"\s+content="([^"]+)"') {
        return $Matches[1]
    }
    if ($Html -match 'content="([^"]+)"\s+name="csrf-token"') {
        return $Matches[1]
    }

    throw 'CSRF token not found on login page.'
}

Write-Host ''
Write-Host ("Le Cameleon smoke - {0}" -f $BaseUrl) -ForegroundColor Cyan
Write-Host ''

Write-Host 'Storefront (expect 200)'
@(
    @{ Name = 'GET /'; Path = '/' },
    @{ Name = 'GET /shop'; Path = '/shop' },
    @{ Name = 'GET /wishlist'; Path = '/wishlist' },
    @{ Name = 'GET /cart'; Path = '/cart' },
    @{ Name = 'GET /login'; Path = '/login' }
) | ForEach-Object {
    $code = Get-StatusCode -Url ($BaseUrl + $_.Path)
    Write-Result -Name $_.Name -Expected 200 -Actual $code
}

Write-Host ''
Write-Host 'Auth gate /admin'
$adminNoFollow = Get-StatusCode -Url "$BaseUrl/admin"
Write-Result -Name 'GET /admin (no follow)' -Expected 302 -Actual $adminNoFollow -Detail 'redirect to login'

$adminFollow = Get-StatusCode -Url "$BaseUrl/admin" -AllowRedirect
Write-Result -Name 'GET /admin (follow)' -Expected 200 -Actual $adminFollow -Detail 'lands on /login'

Write-Host ''
Write-Host 'Expected failures'
$notFoundProduct = Get-StatusCode -Url "$BaseUrl/shop/producto-inexistente-xyz-404"
Write-Result -Name 'GET /shop/{missing} (404)' -Expected 404 -Actual $notFoundProduct

$notFoundPath = Get-StatusCode -Url "$BaseUrl/ruta-fantasma-404"
Write-Result -Name 'GET /ruta-fantasma-404 (404)' -Expected 404 -Actual $notFoundPath

Write-Host ''
Write-Host '403 as customer on /admin'
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$loginPage = Invoke-WebRequest -Uri "$BaseUrl/login" -WebSession $session -UseBasicParsing -TimeoutSec 20
$token = Get-CsrfToken -Html $loginPage.Content

$loginBody = @{
    _token   = $token
    email    = $CustomerEmail
    password = $CustomerPassword
}

try {
    Invoke-WebRequest -Uri "$BaseUrl/login" -Method POST -Body $loginBody -WebSession $session -MaximumRedirection 5 -UseBasicParsing -TimeoutSec 20 | Out-Null
} catch {
    # Login may 302; continue with session cookie
}

$forbidden = Get-StatusCode -Url "$BaseUrl/admin" -Session $session -AllowRedirect
Write-Result -Name 'GET /admin as customer (403)' -Expected 403 -Actual $forbidden -Detail $CustomerEmail

Write-Host ''
Write-Host ("Summary: {0} passed, {1} failed" -f $passed, $failed)
if ($failed -gt 0) {
    exit 1
}

Write-Host 'All smoke checks passed.' -ForegroundColor Green
exit 0
