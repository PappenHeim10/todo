@echo off
for %%a in (*sql) do (
    echo Executing %%a
    mysql -uroot < %%a
    if errorlevel 1 (
        echo Error executing %%a
        exit /b 1
    )
)

echo All SQL files executed successfully.
pause