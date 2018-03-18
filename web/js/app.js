
window.App = {
  web3Provider: null,
  contracts: {},
  instances: {},
  listeInstances: [],
  currentSection: "#listWHCs",

  init: function () {


    return App.initWeb3();
  },

  initWeb3: function () {

    // Is there an injected web3 instance?
    if (typeof web3 !== 'undefined') {
      App.web3Provider = web3.currentProvider;
    } else {

      // If no injected web3 instance is detected, fall back to Ganache
      App.web3Provider = new Web3.providers.HttpProvider('http://localhost:7545');
    }
    web3 = new Web3(App.web3Provider);

    // We load the Contract. This file is the JSON located in the contracts directory.
    $.getJSON('../../../build/contracts/CompanyContract.json', function (data) {
      // Get the necessary contract artifact file and instantiate it with truffle-contract.
      var WHCArtifact = data;
      App.contracts.WHC = TruffleContract(WHCArtifact);
      App.contracts.WHC.setProvider(App.web3Provider);
    });

  },

  loadContract: function (address) {
    console.log("Loading Contract...");
    // We will load the instance we want to play with.
    App.contracts.WHC
      .at(address) //Address of the contractt
      .then(instance => {
        App.instances.WHC = instance;

        App.showPanelWHC();

        return App.actualiserInfos();

      });


  },

  createContract: function () {
    // This will create a new instance of WHC Contract.
    // It will look for variables in HTML DOM.

    var difficulty = $("#difficultyField select").val();
    var bounty = $("#bountyField").val();
    // This creates a new WHC Game.
    Materialize.toast('Publishing on the blockchain', 3000);
    
    App.contracts.WHC.new("password", { value: web3.toWei(bounty, "ether"), gas: 6000000 }).then(function (instance) {
      Materialize.toast('Published on the blockchain, publishing on BDD', 3000) ;
      //Ajax call to save the contract in bdd
      $.ajax({
        url: 	Routing.generate('of_contracts_new_contract_ajax'),
        method: "post",
        data: {bounty: bounty, difficulty: difficulty}
      }).done(function(){
        Materialize.toast('Published on BDD', 3000) ;

      });
    });
  },

  actualiserInfos: function () {
    console.log("Refreshing WHC Display...");

    $("#choiceUser select").empty();
    $("#recapAccounts tbody").empty();



    web3.eth.getBalance(App.instances.WHC.contract.address, function (error, result) {
      if (error) {
        console.error(error);
      } else {
        $("#soldeContrat").empty().append(web3.fromWei(result, "ether").toNumber());
      }
    });
      App.instances.WHC.contract.password.call(function (error, result) {
      if (error) {
        console.log(error);
      } else {
        $("#resultatContrat").empty().append(result);
      }

    });



    return App.refreshingListeInstances();
  },
  refreshingAccount: function () {
    console.log("Refreshing Account Display...");
    web3.eth.getAccounts(function (error, accounts) {
      if (error) {
        console.log(error);
      }

      $.each(accounts, function (index, value) {

        $("#choiceUser select").append('<option value="' + value + '">' + value + '</option>');
        web3.eth.getBalance(value, function (error, result) {
          if (error) {
            console.error(error);
          } else {
            $("#recapAccounts tbody").append("\
          <tr>\
            <td class='mdl-data-table__cell--non-numeric'>"+ value + "</td>\
            <td>25</td>\
            <td>" + web3.fromWei(result, "ether").toNumber() + "</td>\
          </tr>\
          ");

            $("#creditsCount").empty().append(web3.fromWei(result, "ether").toNumber());
          }
        });

      });
    });

  },
  //Refreshes the entire List
  appendDisplayInstance: function (address) {
    web3.eth.getBalance(address, function (error, result) {
      if (error) {
        console.error(error);
      } else {
        //Callback and converting to get the balance.
        var balance = web3.fromWei(result, "ether").toNumber();
        // Append in the front page.
        $("#listeInstances").append("\
        <a href='#' onclick='App.loadContract(\""+ address + "\")'>\
        <div class='col s3' >\
        <div class='card white center black-text'><br />\
        Banque :\
        <h3> "+ balance + "</h3><br />\
        </div>\
        </a>\
        ");

      }
    });

  },
  refreshingListeInstances: function () {
    console.log("Refreshing Liste WHC Display...");
    // We want to know, during the callack, the indice of the contract ( to attach the address to the button).
    // So we need to count how many callbacks have been called until now.
    var callsOverCount = 0;
    // We delete the existing list.
    console.log("Refreshing List Instances ...");
    $("#listeInstances").empty();
    for (var i in App.listeInstances) {
      // For each instances we retrieve the balance and show it.
      App.appendDisplayInstance(App.listeInstances[i].address);
    }
    return App.refreshingAccount();
  },
  bet: function () {
    var parieur = "";
    var mise = $("#mise")["0"].value;
    //creation de la transaction pour le betEven.
    App.instances.WHC.bet({ from: parieur, value: web3.toWei(mise, "ether"), gas: 2000000 }).then(function (result, error) {
      console.log(result);
      App.actualiserInfos();

    });


  },

  lancerWHC: function () {
    var parieur = $("#choiceUser select").val();

    //on lance la transaction pour lancer la WHC
    App.instances.WHC.launch({ from: parieur, gas: 2000000 }).then(function (result, error) {
      if (error) {

        console.log(error);
      } else {
        console.log(result);
        // we actualise informations.
        App.actualiserInfos();
      }
    });


  },
  contractListing: function () {
    var n = 0; // n will be the number of blocks.
    // The main problem of this function is that calls to transactions are non synchronous.
    // But we want to wait for results before refreshing the display of the list of contracts.
    // That's why we count the number of emitted calls to the blockchain.
    // Thanks to the callbacks we also count the number of finished calls.
    // When both counts are equal, we have finish our call, so we can start refreshing the display.
    // Without doing that, the display functions would have been called several times
    // and with the non synchronous calls in it, it would have been a problem.
    var callsCount = 0;
    var callsOverCount = 0;
    var callsrefreshList = 0;
    web3.eth.getBlockNumber(function (error, result) {
      if (error) {
        console.log(error);
      } else {
        console.log("Refreshing List of Contracts...");
        var n = result;

        var instances = []; // This will be the list of contracts addresses.
        for (var i = 0; i <= n; i++) {
          web3.eth.getBlock(i, true, function (error, result) {
            var block = result;
            // We look at all transactions in the block.
            for (var i in block.transactions) {
              // We check if it is a contract creation by looking at the "to" parameter.
              if (block.transactions[i]["to"] == "0x0") {
                // we open the transaction receipt at the address of the transaction
                // to get the address of the contract.
                web3.eth.getTransactionReceipt(block.transactions[i]["hash"], function (error, result) {
                  if (error) {
                    console.log(error);
                  } else {
                    // We now have the address.
                    var address = result["contractAddress"];
                    // We load the instance of WHC Contract with the address we've just found.
                    App.contracts.WHC
                      .at(address)
                      .then(instance => {
                        // We add the instance to the contracts list.
                        instances.push(instance);
                        // We increase the number of calls that are finished.
                        callsOverCount += 1;
                        // DEPRECATED
                        // We have reached the last call so we can launch the display refresh.
                        //if(callsOverCount == callsCount){
                        //App.refreshingListeInstances();
                        //}
                        App.appendDisplayInstance(address);
                      });
                    callsCount += 1;
                    $("#no_games_available").hide();
                  }
                });
              }
            }
          });
        }
        App.listeInstances = instances;

      }// if there are no contracts
      if (callsCount == 0) {
        $("#no_games_available").show();
        return App.refreshingAccount();
      }



    });


  },


  // ROUTES
  changeSection: function (newSection) {
    $(App.currentSection).show().fadeOut(2000, function () {
      $(newSection).show().fadeIn(2000);
      App.currentSection = newSection;
    });
  },

  showListWHCs: function () {
    return App.changeSection("#listWHCs");
  },

  showPanelWHC: function () {
    return App.changeSection("#WHCPanel");
  }
};



$(function () {
  $(window).load(function () {
    App.init();
  });

});


