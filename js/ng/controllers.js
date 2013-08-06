'use strict';

/* Controllers */

angular.module('Peon.controllers', [])
// Statusbar with realtime updates
.controller('CtrlStatusBar', function($scope,$rootScope,$http,$timeout) {
  var prom=[], minRate = 500, req='all=1', oldSettings={}, alertProm;
  $rootScope.alerts=[{type:'success',text:'Welcome back!'}];
  $rootScope.cgminer=true;
  $rootScope.settings={};
  $rootScope.pools={};
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
      if(!$rootScope.cgminer){
        prom.push($timeout($scope.tick, 1000));
      }
      else if(!once){
        prom.push($timeout($scope.tick, $scope.rate));
      }
      req='all=1'; //Set this to '' to disable temp checks 
    });
  }
  $scope.tick();

  // show save button?
  $scope.saveEnable = function() {
    return !angular.equals( $rootScope.settings,oldSettings,true);
  };
  // Sync settings
  // Note: not possible to remove settings!
  $rootScope.sync = function(action,data,alert) {
    action = action || 'settings';
    data = data || 'load';
    $http.get('f_settings.php?'+action+'='+angular.toJson(data)).success(function(d){
      if(alert){
        angular.forEach(d['info'], function(v,k) {$rootScope.alerts.push(v);});// Add to existing
      }
      if(action=='settings'){
        angular.copy(d['data'],$rootScope.settings);
        angular.copy(d['data'],oldSettings);
      }
      else if(action=='pools'){
        $rootScope.pools=d['data']['pools'];
        $scope.tick();
      }
      else if(action=='timezone'){
        $rootScope.settings.time=d.data.time;
        oldSettings.time=d.data.time;
      }
    });
  };
  // Discard settings
  $scope.back = function() {
    angular.copy(oldSettings,$rootScope.settings);
  };
  // Sync settings with delay
  $rootScope.syncDelay = function(ms,action,data,alert) {
    action = action || 'settings';
    data = data || 'load';
    ms = ms || 3000;
    var dothis = function(){
      $rootScope.sync(action,data,alert);
    }
    $timeout(dothis, ms);
  };

  // Load current settings
  $rootScope.syncDelay(200);

  // Load current pools data
  $rootScope.syncDelay(500,'pools');

  // Make alerts disappear after 10 seconds
  $rootScope.$watch('alerts', function(b,a) {
    if(b.length>5){
      $rootScope.alerts.shift();
    }
    if(alertProm){return;}
    if(b.length>1){//Added
      alertProm=$timeout($rootScope.alertDismiss, 2000);
    }
    else if(b.length==1){//Added
      alertProm=$timeout($rootScope.alertDismiss, 10000);
    }
  }, true);
  $rootScope.alertDismiss = function() {
    $('.alert-top').addClass('alert-dismiss');
    $timeout($rootScope.alertShift, 1000);
  };
  $rootScope.alertShift = function() {
    $rootScope.alerts.shift();
    alertProm=false;
  };
})


.controller('CtrlStatus', function($scope) {
  // Enable tooltips
  $('th div').tooltip();
  $('button').tooltip();
})


.controller('CtrlSettings', function($scope,$rootScope) {
  $scope.poolAdd = function() {
    $rootScope.pools.push({});
  };
  $scope.poolRemove = function(index) {
    $rootScope.pools.splice(index,1);
    $scope.poolForm.$setDirty()
  };
  $scope.poolSave = function() {
    $rootScope.sync('pools',$rootScope.pools,true);
    $scope.poolForm.$setPristine();
  };
  $scope.poolBack = function() {
    $rootScope.sync('pools');
    $scope.poolForm.$setPristine();
  };
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
  {selected:true,name:"etc/minepeon.conf"},
  {selected:true,name:"etc/miner.conf"},
  {selected:true,name:"etc/miner.conf.donate"},
  {selected:true,name:"etc/miner.conf.tmp"},
  {selected:true,name:"etc/uipassword"},
  {selected:true,name:"etc/version"}
  ];
  $scope.folders = [
  {selected:true,name:"var/rrd"}
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
          else{
            $scope.files[i].fail=true;
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
          else{
            $scope.files[i].fail=true;
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