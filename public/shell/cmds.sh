sudo yum update -y

# required dependencies
sudo yum install perl net-tools

# disable SELinux
sudo setenforce 0

# Install telnet
yum install telnet -y

# Check Port Number 25
telnet smtp.gmail.com 25