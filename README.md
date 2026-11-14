# docker
docker repository

 added docker installation code file on which is available in the docker website " https://docs.docker.com/engine/install/ubuntu/#installation-methods "

added another file which is modified from chatgpt having non-interactive screen
 

 
    
    sudo docker build . "this is used to create image from docker file "
    
    sudo docker ps -a  "to list all the containers"
    
    sudo docker images  "to list all the images"
    
    sudo docker run -itd -p 8080:80 d0d90c329a74  "to build the container and port forwarding"
    
    sudo docker ps -a
    
    sudo docker stop 56efd32dad96  "to stop the container"
    
    sudo docker rm 56efd32dad96  "to remove the container"
    
    sudo docker rmi webiste:latest  "to remove image"
    
    sudo docker  images
    
    sudo docker ps -a
    
    history  "to view the history of the command used"
