#!/bin/bash

initdb \
&& postgres -D data -r role.sql &
