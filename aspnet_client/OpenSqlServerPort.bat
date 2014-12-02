netsh advfirewall firewall add rule name="Open Port 80" dir=in action=allow protocol=TCP localport=80

@echo =========  SQL Server Ports  ===================
@echo Enabling SQLServer default instance port 1433
netsh advfirewall firewall add rule name="SQL Server" dir=in action=allow protocol=TCP localport=1433
@echo Enabling Dedicated Admin Connection port 1434
netsh advfirewall firewall add rule name="SQL Admin Connection" dir=in action=allow protocol=TCP localport=1434
@echo Enabling Conventional SQL Server Service Broker port 4022
netsh advfirewall firewall add rule name="SQL Service Broker" dir=in action=allow protocol=TCP localport=4022
@echo Enabling Transact SQL/RPC port 135
netsh advfirewall firewall add rule name="SQL Debugger/RPC" dir=in action=allow protocol=TCP localport=135
@echo =========  Analysis Services Ports  ==============
@echo Enabling SSAS Default Instance port 2383
netsh advfirewall firewall add rule name="Analysis Services" dir=in action=allow protocol=TCP localport=2383
@echo Enabling SQL Server Browser Service port 2382
netsh advfirewall firewall add rule name="SQL Browser" dir=in action=allow protocol=TCP localport=2382

@echo =========  Misc Applications  ==============
@echo Enabling HTTP port 80
netsh advfirewall firewall add rule name="HTTP" dir=in action=allow protocol=TCP localport=80
@echo Enabling SSL port 443
netsh advfirewall firewall add rule name="SSL" dir=in action=allow protocol=TCP localport=443
@echo Enabling port for SQL Server Browser Service's 'Browse' Button
netsh advfirewall firewall add rule name="SQL Browser" dir=in action=allow protocol=UDP localport=1434
@echo Allowing multicast broadcast response on UDP (Browser Service Enumerations OK)
netsh firewall set multicastbroadcastresponse ENABLE