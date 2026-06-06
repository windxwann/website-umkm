$phpPath = "C:\xampp2\php\php.exe"
$artisanPath = "c:\Users\RIDWAN\dapoer-jiemas\artisan"
$logPath = "c:\Users\RIDWAN\dapoer-jiemas\storage\logs\satpam-digital.log"

while($true) {
    & $phpPath $artisanPath schedule:run >> $logPath 2>&1
    Start-Sleep -Seconds 60
}
