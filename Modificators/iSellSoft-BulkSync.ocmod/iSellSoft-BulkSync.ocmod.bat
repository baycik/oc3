ROBOCOPY "../../admin/controller/extension/module/" "./upload/admin/controller/extension/module/" iss_bulksync_cron.php iss_bulksync_import.php iss_bulksync_setup.php





ROBOCOPY "../../admin\language\tr-tr\extension\module/iss_bulksync/" "./upload/admin\language\tr-tr\extension\module/iss_bulksync/"  /mir
ROBOCOPY "../../admin\language\en-gb\extension\module/iss_bulksync/" "./upload/admin\language\en-gb\extension\module/iss_bulksync/"  /mir
ROBOCOPY "../../admin/model/extension/module/iss_bulksync/" "./upload/admin/model/extension/module/iss_bulksync/"  /mir
ROBOCOPY "../../admin/view/template/extension/module/iss_bulksync/" "./upload/admin/view/template/extension/module/iss_bulksync/"  /mir
ROBOCOPY "../../admin/view/javascript/iss_bulksync/" "./upload/admin/view/javascript/iss_bulksync/"  /mir

"%ProgramFiles%\WinRAR\WinRAR.exe" a -afzip -ep1 -ibck -r -y -x*.bat -x*.zip "../../../iSellSoft-BulkSync1.0(3.0.x).ocmod.zip" "./*"
PAUSE
