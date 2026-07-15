#Requires -Version 5.1
<#
.SYNOPSIS
  Docker helper for Le Cameleon local development.
.EXAMPLE
  .\docker\dev.ps1 up
  .\docker\dev.ps1 setup
  .\docker\dev.ps1 smoke
  .\docker\dev.ps1 artisan migrate
  .\docker\dev.ps1 down
#>
param(
    [Parameter(Position = 0)]
    [ValidateSet('up', 'down', 'build', 'setup', 'artisan', 'composer', 'npm', 'logs', 'shell', 'ps', 'smoke')]
    [string]$Command = 'up',

    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

function Assert-Docker {
    docker info 1>$null 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "Docker Desktop is not running. Start it and retry."
    }
}

Assert-Docker

switch ($Command) {
    'up' {
        docker compose up -d --build @Args
    }
    'down' {
        docker compose down @Args
    }
    'build' {
        docker compose build @Args
    }
    'setup' {
        if (-not (Test-Path '.env')) {
            Copy-Item '.env.example' '.env'
            Write-Host 'Created .env from .env.example'
        }
        docker compose up -d --build
        docker compose exec app composer install --no-interaction
        docker compose exec app php artisan key:generate --force
        docker compose exec app php artisan migrate --force --seed
        docker compose exec app php artisan storage:link
        Write-Host ''
        Write-Host 'Store:   http://localhost:8080'
        Write-Host 'Mailpit: http://localhost:18025'
        Write-Host 'Vite:    http://localhost:5173'
    }
    'artisan' {
        docker compose exec app php artisan @Args
    }
    'composer' {
        docker compose exec app composer @Args
    }
    'npm' {
        docker compose exec node npm @Args
    }
    'logs' {
        docker compose logs -f @Args
    }
    'shell' {
        docker compose exec app sh
    }
    'ps' {
        docker compose ps
    }
    'smoke' {
        & "$PSScriptRoot\smoke.ps1" @Args
    }
}
