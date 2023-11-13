#!/bin/sh

DIR=".git/hooks/"
if [ -d "$DIR" ]; then
    cp ops/contrib/pre-commit .git/hooks/pre-commit
    chmod +x .git/hooks/pre-commit
fi
