#!/usr/bin/env bash
rsync -avzh --progress /home/frank/Documents/SIT780-API/. lanti@interactive.deakin.edu.au:/home/lanti/public_html/sit780/api
rsync -avzh --progress /home/frank/Documents/SIT780_APP/dist/. lanti@interactive.deakin.edu.au:/home/lanti/public_html/sit780