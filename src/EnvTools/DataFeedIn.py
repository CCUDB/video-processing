#!/usr/bin/env python
#
#   Paramaters <host> <port> <dbname> <video_storage>
#
#   Description: This is wrote for build up an basic database enviroment for this project.
#   Author: FATESAIKOU

from sys import argv
from os import write

import rethinkdb as r
import glob

host = argv[1]
port = argv[2]
dbname = argv[3]
video_storage = argv[4]

""" DB connection """
try:
    conn = r.connect(host, port)
except:
    write(2, 'Rethinkdb connect error!')
    exit(2);

""" Tables connection """
try:
    video_meta = r.db(dbname).table('video_meta')
except:
    write(2, 'Table connect error!')
    exit(2);

""" Get MP4 List """
mp4_list = glob.glob(argv[4] + "/*.mp4")

""" Data Feed In """
for name in mp4_list:
    video_meta.insert({
        'id': name,
        'video_id': video_meta.count().run(conn)
    }).run(conn)

print 'Process Success!'
