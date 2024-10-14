import mysql.connector
import config

class Jobs:
    def __init__(self):
        auth = config.credentials()
        
        print(auth)


job = Jobs()        
