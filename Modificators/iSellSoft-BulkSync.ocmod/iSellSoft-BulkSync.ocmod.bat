ROBOCOPY "../../admin/controller/extension/iss_bulksync/" "./upload/admin/controller/extension/iss_bulksync/" /mir






ROBOCOPY "../../admin/controller/extension/module/iss_bulksync_cron.php" "./upload/admin/controller/extension/module/iss_bulksync_cron.php"
ROBOCOPY "../../admin/controller/extension/module/iss_bulksync_import.php" "./upload/admin/controller/extension/module/iss_bulksync_import.php"
ROBOCOPY "../../admin/controller/extension/module/iss_bulksync_parserlist.php" "./upload/admin/controller/extension/module/iss_bulksync_parserlist.php"




ROBOCOPY "../../admin\language\en-gb\extension\module/iss_bulksync/" "./upload/admin\language\en-gb\extension\module/iss_bulksync/"  /mir
ROBOCOPY "../../admin/model/extension/module/iss_bulksync/" "./upload/admin/model/extension/module/iss_bulksync/"  /mir
ROBOCOPY "../../admin/view/template/extension/module/iss_bulksync/" "./upload/admin/view/template/extension/module/iss_bulksync/"  /mir

"%ProgramFiles%\WinRAR\WinRAR.exe" a -afzip -ep1 -ibck -r -y -x*.bat -x*.zip "../../../iSellSoft-BulkSync1.0(3.0.x).ocmod.zip" "./*"
PAUSE
