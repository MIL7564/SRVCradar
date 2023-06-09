$ports = @(30457)  # Specific port to check

foreach ($port in $ports) {
    $result = Test-NetConnection -Port $port -InformationLevel Quiet

    if ($result.TcpTestSucceeded) {
        Write-Host "Port $port is open"
    }
    else {
        Write-Host "Port $port is closed"
    }
}
