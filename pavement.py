from paver.easy import *

@task
def csstidy():
	sh("csstidy assets/css/style.css --sort_properties=true --sort_selectors=true assets/css/style2.css")
	sh("mv assets/css/style2.css assets/css/style.css")
