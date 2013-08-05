'use strict';

/* Controllers */

angular.module('Peon.controllers', [])
// Statusbar with realtime updates
.controller('CtrlStatusBar', function($scope,$rootScope,$http,$timeout) {
  var prom=[], minRate = 500, req='all=1', oldSettings={};
  $rootScope.settings={};
  $scope.rate = 30000; // Default refresh rate

  // Enable tooltips
  $('span.btn').tooltip({placement:'bottom'});
  $('button').tooltip({container: 'body'});

  // Update values
  $scope.setRate = function(value) {
    angular.forEach(prom, function(p) {$timeout.cancel(p)});// Clean up timeouts
    if(value>=minRate){ // Set new timeout
      $scope.rate=value;
      req='all=1';
      $scope.tick();
    }
    else{$scope.rate=600001;} // Off = 10 minutes
  };

  // Get status and save in rootScope
  $scope.tick = function(once) {
    if($scope.settings.devEnable)req+='&dev=1';  
    $http.get('f_status.php?'+req).success(function(d){
      angular.forEach(d, function(v,k) {$rootScope[k]=v;});      
      if(!once){
        prom.push($timeout($scope.tick, $scope.rate));
      }
      req='all=1'; //Set this to '' to disable temp checks 
    })
  }
  $scope.tick();

  // show save button?
  $scope.saveEnable = function() {
    return !angular.equals( $rootScope.settings,oldSettings,true);
  };
  // Fetch settings
  // Save param in settings, if not set, don't save
  // Note: not possible to remove settings!
  $scope.save = function(param) {
    $http.get('f_settings.php?save='+angular.toJson(param)).success(function(d){
      console.log(d)
      angular.forEach(d, function(v,k) {
        $rootScope.settings[k]=v;
      });
      angular.copy($rootScope.settings,oldSettings);
    });
  };
  $scope.back = function() {
    angular.copy(oldSettings,$rootScope.settings);
  };

  // Load current settings and save in rootScope
  $scope.save(false);
})


.controller('CtrlStatus', function($scope) {
  // Enable tooltips
  $('th div').tooltip();
  $('button').tooltip();
})


.controller('CtrlSettings', function($scope,$rootScope,$http) {
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
  {selected:true,bak:false,name:"etc/minepeon.conf"},
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