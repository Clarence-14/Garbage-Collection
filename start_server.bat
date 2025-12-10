@echo off
echo Starting EcoTrack Server...
echo Open http://localhost:8000 in your browser.
echo Press Ctrl+C to stop.
php -S localhost:8000 -t public
