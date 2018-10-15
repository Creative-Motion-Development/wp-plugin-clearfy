#!/bin/bash

 if [ -z $1 ];
    then
      echo "Ошибка: Вы должны написать коммит";
      exit;
    fi;

git config -f .gitmodules --get-regexp '^submodule\..*\.path$' |

    while read path_key path;
    do
    echo $path;
        if [ $path = "components/update-manager-premium" ];
        then
         break;
        fi;

        #echo "submodule foreach \"git commit -a -m \"$1\"";
        #git submodule foreach "git status"
        git submodule foreach "git commit -a -m \"$1\""
        git submodule foreach "git push origin dev"
    done;

    exit;