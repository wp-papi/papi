# -*- mode: ruby -*-
# vi: set ft=ruby :

# The hostname to use
HOSTNAME = 'papi'

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "frozzare/isodev"

  # Virtualbox configuration.
  config.vm.provider :virtualbox do |v|
    v.customize ['modifyvm', :id, '--memory', 1024]
  end

  # Set private network ip
  config.vm.network :private_network, ip: '192.168.66.6'

  # Set hostname
  config.vm.hostname = HOSTNAME

  # Set shell provision script
  config.vm.provision :shell, :path => './bin/vagrant.sh'

  # Set synced folder
  config.vm.synced_folder '.', '/vagrant', type: "nfs", mount_options: ['rw', 'vers=3', 'tcp', 'fsc' ,'actimeo=2']
end
