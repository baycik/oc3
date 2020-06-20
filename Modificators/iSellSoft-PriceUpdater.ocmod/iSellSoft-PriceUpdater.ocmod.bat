ROBOCOPY "../../catalog/controller/extension/module/" "./upload/catalog/controller/extension/module/" iss_price_updater.php
ROBOCOPY "../../admin/controller/extension/module/" "./upload/admin/controller/extension/module/" iss_price_updater_monitor.php


ROBOCOPY "../../admin/view/template/extension/module/iss_priceupdater/" "./upload/admin/view/template/extension/module/iss_priceupdater/"  /mir


"%ProgramFiles%\WinRAR\WinRAR.exe" a -afzip -ep1 -ibck -r -y -x*.bat -x*.zip "../../../iSellSoft-PriceUpdater1.0(3.0.x).ocmod.zip" "./*"
PAUSE
