#Dockerfile for c360ui nodejs application
#This Dockerfile builds the c360 production image after the stage instance passed the QA
#1. get c369ui base image
#tag the latest dockerhub node image release
FROM c360ui_stage_img
#2. The dockerfile created and image will be managed by
MAINTAINER Zhongqiu Du version: 0.0.1
#3. Apllication working dir and expose port(s)
WORKDIR /c360UI
EXPOSE 8080 80
#4. Adding source code into the working dir
ADD ./app.js /c360UI/
ADD ./config.js /c360UI/
ADD ./package.json /c360UI/
ADD ./auth/ /c360UI/auth/
ADD ./bin/ /c360UI/bin/
ADD ./models/ /c360UI/models/
ADD ./public/ /c360UI/public/
ADD ./routes /c360UI/routes/
ADD ./services /c360UI/services/
ADD ./views /c360UI/views/
#let's add configuration and tools also
ADD ./etc/ /c360UI/etc/
ADD ./scripts/ /c360UI/scripts/
#5. RUN npm start
CMD ["node", "/c360UI/bin/www"]
#$ docker build --rm -t c360ui_img:<version> .
