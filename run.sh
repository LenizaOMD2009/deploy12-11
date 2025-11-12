#!/bin/bash

composer install --no-dev --no-progress -a

service nginx reaload