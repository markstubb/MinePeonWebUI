'use strict';

/* Controllers */

angular.module('Peon.controllers', [])
.controller('CtrlStatus', function($scope,$http,$timeout) {
  $scope.stat = {"status":"Loading status..."};
  $scope.refresh = 2000;


  $scope.prettystatus = function() {
    return JSON.stringify($scope.stat, undefined, 2);
  };

  // Update values
  var oldcpu={"idle":0,"tot":0};
  (function tick() {
    $http.get('f_status.php').success(function(d){
      if(d.success){
        $scope.stat=d;
        $scope.load=(d.cpu.idle-oldcpu.idle)/(d.cpu.tot-oldcpu.tot);
        oldcpu.idle=d.cpu.idle;
        oldcpu.tot=d.cpu.tot;
      }
      $timeout(tick, $scope.refresh);
    })
  })();

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