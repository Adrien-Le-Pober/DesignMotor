#!/bin/sh
if [ ! -f "angular.json" ]; then
    echo "ERROR: Angular project configuration not found."
    exit 1
fi
ng serve --host 0.0.0.0 --port 4200 --poll 1000