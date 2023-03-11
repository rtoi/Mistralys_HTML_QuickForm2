@echo off

set AnalysisLevel=6
set OutputFile=.\result.txt
set ConfigFile=.\config.neon
set BinFolder=..\..\vendor\bin

cls

echo -------------------------------------------------------
echo RUNNING PHPSTAN @ LEVEL %AnalysisLevel%
echo -------------------------------------------------------
echo.

call %BinFolder%\phpstan analyse -l %AnalysisLevel% -c %ConfigFile% --memory-limit=900M > %OutputFile%

start "" "%OutputFile%"
