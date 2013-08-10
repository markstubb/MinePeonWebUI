'use strict';

/* Controllers */

angular.module('Peon.controllers', [])


// Main: stores status
.controller('CtrlMain', function($scope,$http,$timeout) {
  // Settings
  $scope.settings={};
  $scope.settingsMaster={};
  // Pools
  $scope.pools={};
  $scope.options={};
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
        $scope.settings=angular.copy(d['data']);
        $scope.settingsMaster=angular.copy(d['data']);
      }
      else if(action=='pools'){
        $scope.pools=d['data']['pools'];
      }
      else if(action=='options'){
        $scope.options=d['data'];
      }
      else if(action=='timezone'){
        $scope.settings.time=d.data.time;
        $scope.settingsMaster.time=d.data.time;
      }
    });
  }
  $scope.syncDelay = function(ms,action,data,alert) {
    action = action || 'settings';
    data = data || false;
    ms = ms || 1000;
    var syncNow = function(){
      $scope.sync(action,data,alert);
    }
    return $timeout(syncNow, ms);
  }

  // Sync settings
  $scope.sync('settings')
  $scope.syncDelay(500,'pools');
  $scope.syncDelay(900,'options');

  // Show save button? Should be done with ngform.$dirty
  $scope.saveHide = function() {
    return angular.equals( $scope.settings,$scope.settingsMaster);
  };
  // Discard settings
  $scope.back = function() {
    $scope.settings=angular.copy($scope.settingsMaster);
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
    console.log($scope.settingsMaster)
    console.log($scope.settings)
  }
  $scope.tick(0,1);

  // Set rate and update status
  $scope.setRate = function(value) {
    angular.forEach($scope.statusProm, function(p) {$timeout.cancel(p)});// Clean up timeouts
    $scope.statusRate=value>=500?value:600001;
    $scope.tick(0,1);
  };
  
  $scope.optionArr={};
  $scope.$watch('options', function(b,a) {
    angular.forEach(b, function(v,k) {$scope.optionArr[k]={key:k};});
  });
})


// Alert: removes alerts after some time
.controller('CtrlAlert', function($scope,$timeout) {
  var alertProm;
  // Make alerts disappear after 10 seconds
  $scope.$watch('alerts', function(b,a) {
    if(alertProm){}
      else if(b.length>3){
        alertProm=$timeout($scope.alertDismiss, 1);
      }
      else if(b.length>1){
        alertProm=$timeout($scope.alertDismiss, 1000);
      }
      else if(b.length==1){
        alertProm=$timeout($scope.alertDismiss, 3000);
      }
    }, true);
  $scope.alertDismiss = function() {
    alertProm=$timeout($scope.alertShift, 1010);
    $('.alert-top').addClass('alert-dismiss');
  };
  $scope.alertShift = function() {
    $scope.alerts.shift();
    alertProm=false;
  };
})


// Statusbar with realtime updates
.controller('CtrlStatusBar', function($scope,$timeout) {
  // Enable tooltips
  $('span.btn').tooltip({placement:'bottom'});
  $('button').tooltip({container: 'body'});

  // timer last update
  $scope.counter=0;
  $scope.countProm = null;
  $scope.countLast = Date.now();

  var count = function () {
    $scope.countProm = $timeout(count, 1000);
    $scope.counter++;
  };

  $scope.$watch('status.time', function() {
    $scope.counter=0;
    $scope.countLast = Date.now();
    if($scope.countProm){
      $timeout.cancel($scope.countProm);
    }
    count();
  }, true);
})


.controller('CtrlStatus', function($scope) {
  // Enable tooltips
  $('th div').tooltip();
})


.controller('CtrlMiner', function($scope) {

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

  $scope.optionAdd = function() {
    $scope.options.push({});
  };
  $scope.optionRemove = function(index) {
    $scope.options.splice(index,1);
    $scope.optionForm.$setDirty()
  };
  $scope.optionSave = function() {
    $scope.sync('options',$scope.options,1);
    $scope.optionForm.$setPristine();
  };
  $scope.optionBack = function() {
    $scope.sync('options');
    $scope.optionForm.$setPristine();
  };
})


.controller('CtrlSettings', function($scope) {
})


.controller('CtrlBackup', function($scope,$http) {
  $scope.thisFolder = "/opt/minepeon/";
  $scope.backupFolder = "/opt/minepeon/etc/backup/";
  $scope.backupName = GetDateTime()+"/";
  $scope.backups = [];
  $scope.restoring = 0;
  $scope.items = [
  {selected:true,name:"etc/miner.user.conf"},
  {selected:true,name:"etc/miner.conf"},
  {selected:true,name:"etc/uipassword"},
  {selected:true,name:"etc/version"},
  {selected:true,name:"etc/minepeon.conf"},
  {selected:true,name:"var/rrd"}
  ];
  
  $scope.addItem = function() {
    $scope.items.push({selected:true,name:$scope.newItem});
    $scope.newItem = '';
  };
  $scope.selItem = function() {
    var count = 0;
    angular.forEach($scope.items, function(item) {
      count += item.selected ? 1 : 0;
    });
    return count;
  };
  

  $scope.backup = function() {
    var count = 0;

    // These get requests need some timeout I think, but it also works like this.
    angular.forEach($scope.items, function(f,i) {
      if(f.selected){
        $http.get('f_copy.php?src='+$scope.thisFolder+f.name+'&dst='+$scope.backupFolder+$scope.backupName+f.name).success(function(d){
          console.log(d.success);
          if(d.success){
            $scope.items[i].bak=true;
            $scope.items[i].selected=false;
          }
          else{
            $scope.items[i].fail=true;
          }
        });
      }
    });
  };


  $scope.choose = function(i) {
    $scope.restoring=i;
  };

  $scope.restore = function() {
    console.log($scope.restoring)
  };

  $scope.reload = function() {
    $http.get('f_backup.php').success(function(d){
      if(d.data){
        $scope.backups=d.data;
      }
    });
  };
  $scope.reload();
});


function GetDateTime() {
  var now = new Date();
  return [[now.getFullYear(),AddZero(now.getMonth() + 1),AddZero(now.getDate())].join(""), [AddZero(now.getHours()), AddZero(now.getMinutes())].join("")].join("-");
}

function AddZero(num) {
  return (num >= 0 && num < 10) ? "0" + num : num + "";
}