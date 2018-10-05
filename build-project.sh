#!/bin/bash 
git checkout dev
git submodule init
git submodule update
git submodule foreach "git checkout dev"