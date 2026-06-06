Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "powershell.exe -ExecutionPolicy Bypass -WindowStyle Hidden -File ""C:\Users\RIDWAN\dapoer-jiemas\satpam-worker.ps1""", 0
Set WshShell = Nothing
