import mysql.connector
import config
import json
import re

class Jobs:
    def __init__(self):
        auth = config.credentials()        
        self.db = mysql.connector.connect(
            host=auth['HOST'],
            user=auth['USER'],
            password=auth['PASSWORD'],
            database=auth['DATABASE']
        )

    def executeJob(self, job):        
        getattr(__import__(f'modules.{job["class"]}', fromlist=[job["class"]]), job['method'])(job['params'])           

    def scheduleJobs(self):
        cursor = self.db.cursor()
        cursor.execute("SELECT exec FROM jobs WHERE exec_status = 'P' AND active_row = 'Y' LIMIT 5")
        jobs = cursor.fetchall()

        for job in jobs:
            exec_content = json.loads(job[0])

            if re.search(r'^[A-Z]\S+\/\S+$', exec_content['job']) is None:
                print('Invalid job format')
                continue

            job_param = exec_content['job'].split('/')

            job_param = {
                "class": job_param[0],
                "method": job_param[1],
                "params": {
                    "user": exec_content['user'],
                    "content": exec_content['content']
                }
            }
            
            self.executeJob(job_param)
            
        cursor.close()



job = Jobs()
job.scheduleJobs()
