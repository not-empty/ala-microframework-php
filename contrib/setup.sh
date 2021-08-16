#!/bin/sh

DIR=".git/hooks/"
if [ -d "$DIR" ]; then
    cp contrib/pre-commit .git/hooks/pre-commit
    chmod +x .git/hooks/pre-commit
fi
