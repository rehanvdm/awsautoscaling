# 3 ways to Autoscale on AWS

## Requirements

 0. Basic AWS knowledge
 1. Have the AWS CLI installed and configured
 2. Have NPM installed
 3. Do a search and replace in the repo and replace ``--profile rehan`` with you ``--profile <YOUR PROFILE NAME GOES HERE>``

## How to deploy the solutions

Each folder represents a solution. For ElasticBeanstalk (EB) and ECS Fargate it is a 2 page PHP application.
The index page just reporting the Session ID, Server Name and your IP onto the page. 
The second page is a fibonacci calculation to waste some server resources. This is called by the autoscaling load tests 
since the CPU is being used as the autoscaling metric, it just allows us to test the autoscaling in action earlier on in the tests.

The Lambda app just echos your IP back to you, there isn't a call to for testing load. We want to spend as little time as
possible in the function to reduce costs. Out of experience I know these scale excellently keeping in mind the few extra 
milli seconds for a cold start. 

Within each folder the `cf.yaml` file is the CloudFormation YAML used for that solution.

### ElasticBeanstalk 

*Upload the ZIP in **/app/app.zip** to an S3 Bucket, then change lines 72 and 73, the Bucket and Key values of the cf.yaml file*

EB will deploy a classic load balancer and 2 EC2 Instances pulling code from the S3 bucket as setup in the cf.yaml file.

```
cd ElasticBeanstalk
npm run deploy
```
To view the progress and all outputs created by this stack from the CLI use:
``` 
npm run describe_stack
```
This will provide the ALB DNS Name that can be copied and quickly pasted into the browser for a test.

### Fargate 

Deploys an Application Load Balancer (ALB), ECS Fargate Cluster, Service and 2 Tasks. It uses the exact same code as EB, except  
that this code is now in a public docker image. The only change that needs to be made is to fill in the ``VPC, Subnet1 and Subnet2`` 
parameters at the top of the cf.yaml file with your values. 

```
cd Fargate
npm run deploy
```
To view the progress and all outputs created by this stack from the CLI use:
``` 
npm run describe_stack
```
This will provide the ALB DNS Name that can be copied and quickly pasted into the browser for a test.

### Lambda & ALB 
Deploys an ALB that targets a Lambda function running a NodeJS app. It does not have an */app* directory as the few
lines(9) of code for the Lambda are defined in the cf.yaml file. The only change that needs to be made is to fill in the ``VPC, Subnet1 and Subnet2`` 
parameters at the top of the cf.yaml file with your values

```
cd LambdaALB
npm run deploy
```
To view the progress and all outputs created by this stack from the CLI use:
``` 
npm run describe_stack
```
This will provide the ALB DNS Name that can be copied and quickly pasted into the browser for a test.


## Tests 

Artillery is used to do load testing. A simple script that does 4 requests per second for 10 minutes on the /fibonacci.php 
path. Every 10 seconds you get nice stats like avg, min, max, p95, p99. CloudWatch should be used to visually verify how each 
solution autoscales in and out to meet demand.

``` 
cd tests\load
npm run loadtest
```