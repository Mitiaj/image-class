Vagrant.configure("2") do |config|
  config.vm.box = "debian-wheezy72-x64-vbox43"
  config.vm.box_url = "http://box.puphpet.com/debian-wheezy72-x64-vbox43.box"

  config.vm.network "private_network", ip: "192.168.56.149"
	config.vm.hostname = "suzie.test"
	config.hostsupdater.aliases = ["www.suzie.test"]
	config.vm.network "forwarded_port", guest: 22, host: 9535

  config.vm.synced_folder "./project", "/var/www", id: "vagrant-root", :nfs => false

  config.vm.usable_port_range = (2200..2250)
  config.vm.provider :virtualbox do |virtualbox|
    virtualbox.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    virtualbox.customize ["modifyvm", :id, "--memory", "512"]
    virtualbox.customize ["setextradata", :id, "--VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  end

  config.vm.provision :shell, :path => "puphpet/shell/initial-setup.sh"
  config.vm.provision :shell, :path => "puphpet/shell/update-puppet.sh"
  config.vm.provision :shell, :path => "puphpet/shell/librarian-puppet-vagrant.sh"
  config.vm.provision :puppet do |puppet|
    puppet.facter = {
      "ssh_username" => "vagrant"
    }

    puppet.manifests_path = "puphpet/puppet/manifests"
    puppet.options = ["--verbose", "--hiera_config /vagrant/puphpet/puppet/hiera.yaml", "--parser future"]
  end

  config.vm.provision :shell, :path => "puphpet/shell/execute-files.sh"




  config.ssh.username = "vagrant"

  config.ssh.shell = "bash -l"

  config.ssh.keep_alive = true
  config.ssh.forward_agent = false
  config.ssh.forward_x11 = false
  config.vagrant.host = :detect
end

