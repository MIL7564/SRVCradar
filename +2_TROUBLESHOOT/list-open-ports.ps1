$ports = 1..65535  # Range of ports to check
$random = New-Object System.Random

do {
    $randomPort = $ports | Get-Random
    $result = Test-NetConnection -Port $randomPort -InformationLevel Quiet

    if ($result.TcpTestSucceeded) {
        Write-Host "Open port found: $randomPort"
        break
    } else {
        Write-Host "Port $randomPort is not available"
    }
} while ($true)
