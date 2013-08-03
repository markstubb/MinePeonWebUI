'use strict';

/* Controllers */

angular.module('Peon.controllers', [])
// Statusbar with realtime updates
.controller('CtrlStatusBar', function($scope,$http,$timeout) {
  var prom=[], minRate = 500;
  $scope.rate = 5000; // Default refresh rate

  $(".btn").tooltip({'placement':'bottom'}); // Enable tooltips on status items

  $scope.setRate = function(value) {
    $timeout.cancel(prom.pop()); // Cancel current timeout
    if(prom.length>20){angular.forEach(prom, function(p) {$timeout.cancel(p)}); }// Clean up more timeouts
    if(value>=minRate){ // Set new timeout
      $scope.rate=value;
      $scope.tick();
    }
    else{$scope.rate=600001;} // Off = 10 minutes
  };

  // Update values
  $scope.cpu = 0; // Prevents displaying NaN
  var prevCpuIdle=0,prevCpuTot=0; // To compute beter cpu usage
  $scope.tick = function(once) {
    $http.get('f_statusbar.php').success(function(d){
      if(d.success){
        $scope.statbar=d;
        $scope.cpu=1-(d.cpuIdle-prevCpuIdle)/(d.cpuTot-prevCpuTot);
        prevCpuIdle=d.cpuIdle;
        prevCpuTot=d.cpuTot;
      }
      if(!once){
        prom.push($timeout($scope.tick, $scope.rate));
      }
    })
  }
  
  $scope.tick();
})
.controller('CtrlStatus', function($scope,$http,$timeout) {
  // Enable tooltips and tablesorter
  $("th div").tooltip();
  $(".tablesorter").tablesorter();

  // Debug data
  $scope.pools = [ {
    "POOL": 0,
    "URL": "http://stratum.mining.eligius.st:3334",
    "Status": "Alive",
    "Priority": 0,
    "LongPoll": "N",
    "Getworks": 1076,
    "Accepted": 5043,
    "Rejected": 6,
    "Discarded": 2151,
    "Stale": 0,
    "GetFailures": 0,
    "RemoteFailures": 0,
    "User": "1BveW6ZoZmx31uaXTEKJo5H9CK318feKKY",
    "LastShareTime": 1375501281,
    "Diff1Shares": 20306,
    "ProxyType": "",
    "Proxy": "",
    "DifficultyAccepted": 20142,
    "DifficultyRejected": 24,
    "DifficultyStale": 0,
    "LastShareDifficulty": 4,
    "HasStratum": true,
    "StratumActive": true,
    "StratumURL": "stratum.mining.eligius.st",
    "HasGBT": false,
    "BestShare": 40657
  }
  ];
  $scope.devs = [ {
    "Name": "Hoeba",
    "ID": 0,
    "Temperature": 24,
    "MHS5s": 195665,
    "MHSav": 213462,
    "LongPoll": "N",
    "Getworks": 1076,
    "Accepted": 1324,
    "Rejected": 1,
    "HardwareErrors": 46,
    "Utility": 1.2,
    "LastShareTime": 1375502781
  }
  ];

  var prom=[], minRate = 500;
  $scope.rate = 5000; // Default refresh rate

  $scope.setRate = function(value) {
    $timeout.cancel(prom.pop()); // Cancel current timeout
    if(prom.length>20){angular.forEach(prom, function(p) {$timeout.cancel(p)}); }// Clean up more timeouts
    if(value>=minRate){ // Set new timeout
      $scope.rate=value;
      $scope.tick();
    }
    else{$scope.rate=600001;} // Off = 10 minutes
  };

  // Update values
  $scope.tick = function(once) {
    $http.get('f_status.php').success(function(d){
      if(d.success){
        $scope.stat=d;
        $scope.pools=d.pools;
        $scope.devs=d.devs;
        $scope.dtot=d.dtot;
      }
      if(!once){
        prom.push($timeout($scope.tick, $scope.rate));
      }
    })
  }
  
  prom.push($timeout($scope.tick, $scope.rate));
})
.controller('CtrlRestore', function($scope,$http) {
  $scope.thisFolder = "/opt/minepeon/";
  $scope.backupFolder = "/opt/minepeon/etc/backup/";

  $scope.folders={};
  $scope.folderdata = { index: 0 };

  $http.get('f_restore_list.php').success(function(d){
    if(d.success){
      $scope.folders=d.folders;
    }
  });

  $scope.restore = function() {
  };
})
.controller('CtrlBackup', function($scope,$http) {
  $scope.thisFolder = "/opt/minepeon/";
  $scope.backupFolder = "/opt/minepeon/etc/backup/";
  $scope.backupName = GetDateTime()+"/";
  $scope.files = [
  {selected:true,bak:false,name:"etc/miner.conf"},
  {selected:true,bak:false,name:"etc/miner.conf.donate"},
  {selected:true,bak:false,name:"etc/miner.conf.tmp"},
  {selected:true,bak:false,name:"etc/uipassword"},
  {selected:true,bak:false,name:"etc/version"}
  ];
  $scope.folders = [
  {selected:true,bak:false,name:"var/rrd"}
  ];

  $scope.addFile = function() {
    $scope.files.push({selected:true,name:$scope.newFile});
    $scope.newFile = '';
  };
  $scope.selFile = function() {
    var count = 0;
    angular.forEach($scope.files, function(file) {
      count += file.selected ? 1 : 0;
    });
    return count;
  };
  $scope.addFolder = function() {
    $scope.folders.push({selected:true,name:$scope.newFolder});
    $scope.newFolder = '';
  };
  $scope.selFolder = function() {
    var count = 0;
    angular.forEach($scope.folders, function(folder) {
      count += folder.selected ? 1 : 0;
    });
    return count;
  };

  $scope.backup = function() {
    var count = 0;

    // These get requests need some timeout I think, but it also works like this.
    angular.forEach($scope.files, function(f,i) {
      if(f.selected){
        $http.get('f_copy.php?src='+$scope.thisFolder+f.name+'&dst='+$scope.backupFolder+$scope.backupName+f.name).success(function(d){
          console.log(d.success);
          if(d.success){
            $scope.files[i].bak=true;
            $scope.files[i].selected=false;
          }
        });
      }
    });
    angular.forEach($scope.folders, function(f,i) {
      if(f.selected){
        $http.get('f_copy.php?src='+$scope.thisFolder+f.name+'&dst='+$scope.backupFolder+$scope.backupName+f.name).success(function(d){
          console.log(d.success);
          if(d.success){
            $scope.folders[i].bak=true;
            $scope.folders[i].selected=false;
          }
        });
      }
    });
  };
});


function GetDateTime() {
  var now = new Date();
  return [[now.getFullYear(),AddZero(now.getMonth() + 1),AddZero(now.getDate())].join(""), [AddZero(now.getHours()), AddZero(now.getMinutes())].join("")].join("-");
}

function AddZero(num) {
  return (num >= 0 && num < 10) ? "0" + num : num + "";
}