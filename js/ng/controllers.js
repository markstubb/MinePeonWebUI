'use strict';

/* Controllers */

angular.module('Peon.controllers', [])


// Main: stores status
.controller('CtrlMain', function($scope,$http,$timeout) {
  // Settings
  $scope.settings={};
  var oldSettings={};
  // Pools
  $scope.pools={};
  // Status
  $scope.status={};
  $scope.status.minerUp=true;
  $scope.status.minerDown=false;
  $scope.statusProm=[];
  $scope.statusRate = 30000; // Default refresh rate
  // Alerts
  $scope.alerts=[{type:'success',text:'Welcome back!'}];

  // Sync settings
  // Note: not possible to remove settings!
  $scope.sync = function(action,data,alert) {
    action = action || 'settings';
    data = data || 'load';
    $http.get('f_settings.php?'+action+'='+angular.toJson(data)).success(function(d){
      if(alert){
        angular.forEach(d['info'], function(v,k) {$scope.alerts.push(v);});// Add to existing
      }
      if(action=='settings'){
        angular.copy(d['data'],$scope.settings);
        angular.copy(d['data'],oldSettings);
      }
      else if(action=='pools'){
        $scope.pools=d['data']['pools'];
      }
      else if(action=='timezone'){
        $scope.settings.time=d.data.time;
        oldSettings.time=d.data.time;
      }
    });
  };
  // Sync settings with delay
  $scope.syncDelay = function(ms,action,data,alert) {
    action = action || 'settings';
    data = data || 'load';
    ms = ms || 1000;
    var dothis = function(){
      $scope.sync(action,data,alert);
    }
    $timeout(dothis, ms);
  };

  // show save button?
  $scope.saveEnable = function() {
    return !angular.equals( $scope.settings,oldSettings,true);
  };
  // Discard settings
  $scope.back = function() {
    angular.copy(oldSettings,$scope.settings);
  };

  // Get status and save in scope
  $scope.tick = function(once,all) {
    $http.get('f_status.php?'+($scope.settings.devEnable?'dev=1&':'')+(all?'all=1':'')).success(function(d){
      angular.forEach(d.info,   function(v,k) {$scope.alerts.push(v);});// Add to existing
      angular.forEach(d.status, function(v,k) {$scope.status[k]=v;});// Overwrite existing
      if($scope.status.minerDown){
        $scope.statusProm.push($timeout($scope.tick, 1000));
      }
      else if(!once){
        $scope.statusProm.push($timeout($scope.tick, $scope.statusRate));
      }
    });
  }
  $scope.tick(0,1);

  // Load current settings
  $scope.syncDelay(500);
})


// Alert: removes alerts after some time
.controller('CtrlAlert', function($scope,$timeout) {
  var alertProm;
  // Make alerts disappear after 10 seconds
  $scope.$watch('alerts', function(b,a) {
    if(b.length>5){
      $scope.alerts.shift();
    }
    if(alertProm){return;}
    else if(b.length>1){//Added
      alertProm=$timeout($scope.alertDismiss, 1000);
    }
    else if(b.length==1){//Added
      alertProm=$timeout($scope.alertDismiss, 3000);
    }
  }, true);
  $scope.alertDismiss = function() {
    $('.alert-top').addClass('alert-dismiss');
    $timeout($scope.alertShift, 1000);
  };
  $scope.alertShift = function() {
    $scope.alerts.shift();
    alertProm=false;
  };
})


// Statusbar with realtime updates
.controller('CtrlStatusBar', function($scope,$http,$timeout) {
  var minRate = 500;

  // Enable tooltips
  $('span.btn').tooltip({placement:'bottom'});
  $('button').tooltip({container: 'body'});

  // Set rate and update status
  $scope.setRate = function(value) {
    angular.forEach($scope.statusProm, function(p) {$timeout.cancel(p)});// Clean up timeouts
    $scope.statusRate=value>=minRate?value:600001;
    $scope.tick(1,1);
  };
})


.controller('CtrlStatus', function($scope) {
  // Enable tooltips
  $('th div').tooltip();
})


.controller('CtrlSettings', function($scope) {
  // Load current settings
  $scope.syncDelay(100);

  // Load current pools data
  $scope.syncDelay(1000,'pools');

  $scope.poolAdd = function() {
    $scope.pools.push({});
  };
  $scope.poolRemove = function(index) {
    $scope.pools.splice(index,1);
    $scope.poolForm.$setDirty()
  };
  $scope.poolSave = function() {
    $scope.sync('pools',$scope.pools,1);
    $scope.poolForm.$setPristine();
  };
  $scope.poolBack = function() {
    $scope.sync('pools');
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