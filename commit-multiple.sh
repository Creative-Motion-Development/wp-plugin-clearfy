#!/bin/bash

if [ -z $1 ];
   then
     echo "Error: You must write a commit!";
     exit;
   fi;

PLUGIN_DIR=$(pwd);
COMMIT=$1;

git config -f .gitmodules --get-regexp '^submodule\..*\.path$' |
    while read path_key path;
    do
        if [ -d $PLUGIN_DIR/$path ]; then

             cd $PLUGIN_DIR/$path;
             echo "Viewing submodule $path";

             if [[ $(git status --porcelain) ]]; then
                 echo "There are uncommented changes!";
                 git add --all;
                 git commit -m "$COMMIT";
                 git pull origin dev
                 git push origin dev
             else
                 echo "No changes.";
             fi;
        fi;
    done;
exit;