# mycms
面向企业与个人的简单高效的内容管理系统(CMS)
./action 所有前台需要使用的功能程序  
./admin 网站后台，为了安全可改名使用。  
./data/cache 缓存目录，需要可删写权限。  
./data/log 日志记录文件，后台操作记录，内容统计日志，错误日志等  
./data/attachment 后台设置的图片和附件上传目录，需要可删写权限  
./source/ 功能源代码目录 
./data/templates模板目录  
./wap 手机版  
./main.php 前台程序的主入口。安全控制接口  
所有后台运行的文件都在admin，如果网站长时间不需要使用后台，可以删除或者移走admin目录。