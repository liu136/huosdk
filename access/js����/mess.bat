@echo off
SET BASEPATH=E:\PHPProject\sdk\access\public
rem 设置压缩JS文件的根目录，脚本会自动按树层次查找和压缩所有的JS
SET JSFOLDER=%BASEPATH%h5\index\js
rem 混淆后的js代码路径
SET JSMESSPATH=%BASEPATH%\sdkh5\index\js\
xcopy %BASEPATH%h5 %BASEPATH%\sdkh5 /s /e /y
echo looking for js file
chdir /d %JSFOLDER%
for /r  . %%a in (*.js)  do (
	echo  messing %%~nxa
	uglifyjs %%a  -m -c -e -o %JSMESSPATH%%%~nxa
)
rem  uglifyjs %BASEPATH%\login.php  -m -c -e -o %JSMESSPATH%\login.php
echo success
