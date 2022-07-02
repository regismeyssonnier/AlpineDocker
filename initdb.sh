#!/bin/bash

createdb | psql -f database.sql
