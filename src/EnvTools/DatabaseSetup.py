#!/usr/bin/env python
#
#   Paramaters <host> <port> <dbname>
#
#   Description: This is wrote for build up an basic database enviroment for this project.
#   Author: FATESAIKOU

from sys import argv
from os import write

import rethinkdb as r

host = argv[1]
port = argv[2]
dbname = argv[3]

""" DB connection """
try:
    conn = r.connect(host, port)
except:
    write(2, 'Rethinkdb connect error!')
    exit(2);

""" Database creation """
try:
    r.db_create(dbname).run(conn)
except:
    write(2, 'DB %s create error!' % dbname)
    exit(2);

""" Tables creation """
try:
    r.db(dbname).table_create('data_meta').run(conn)
    r.db(dbname).table_create('content_data').run(conn)
except:
    write(2, 'Table create error!')
    exit(2);

print 'Process Success!'
