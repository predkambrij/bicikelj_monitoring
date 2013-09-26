Script for cacti scriptserver
============================
It will monitor number of available bicikelj bikes on bicikelj bike stations (relevant for people which is using it in Ljubljana).

Script is parsing json which is provided in following project (details are written in the beggining of the script): https://github.com/zejn/prometapi
Currently is hardcoded public server so if you just use that script it should work (I suggest that you're using that or another public server just for load balancing the original one).

Usage:
- import cacti dataquery template or create your own (here is good tutorial, just use Get Script Server Data (Indexed) for data input method in data template)
- add the graphs

Known issues:
- in cacti 0.8.8a grpah title is cut off after fixed number of characters
- Slovenian special characters are omitted in names


