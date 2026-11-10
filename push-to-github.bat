@echo off
chcp 65001 >nul
echo ============================================================
echo   Роҳнамои автоматии фиристодани лоиҳа ба GitHub (Push)
echo ============================================================
echo.
echo Қадами 1: Дар саҳифаи GitHub (https://github.com/new) репозиторийи
echo навро бо номи railway-tickets созед (Add README-ро фаъол накунед).
echo.
set /p REPO_URL="Қадами 2: Суроғаи репозиторийи GitHub-ро ворид намоед: "

if "%REPO_URL%"=="" (
    echo Хатогӣ: Суроға ворид нашуд!
    pause
    exit /b
)

echo.
echo Дар ҳоли пайвастшавӣ ба GitHub ва фиристодани шохаи main ва тегҳои lab-01...lab-05...
git remote remove origin >nul 2>&1
git remote add origin %REPO_URL%
git branch -M main
git push -u origin main --tags
git push -u origin main

echo.
echo ============================================================
echo   ТАБРИК! Ҳамаи файлҳо ва тегҳои лабораторӣ ба GitHub рафтанд!
echo ============================================================
pause
