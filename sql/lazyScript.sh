#!/bin/bash

mysql cplop < ghostMatchSP-cleanup.sql
mysql cplop < ghostMatchSP-setup.sql
