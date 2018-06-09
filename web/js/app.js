
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
      console.log(WHCArtifact);
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
  randomString: function (len) {
    charSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var randomString = '';
    for (var i = 0; i < len; i++) {
      var randomPoz = Math.floor(Math.random() * charSet.length);
      randomString += charSet.substring(randomPoz, randomPoz + 1);
    }
    return randomString;
  },
  createContract: function () {
    // This will create a new instance of WHC Contract.
    // It will look for variables in HTML DOM.
    $('#formCreatingContract').hide();
    $('#loading').show();
    var difficulty = $("#difficultyField select").val();
    var company = $("#button_create_contract").attr("company");
    var bounty = $("#bountyField").val();
    var name = $("#nameField").val();
    var description = $("#descriptionField").val();
    var randomString = App.randomString(10);
    // This creates a new WHC Game.
    $("#titleAction").html("We are generating your contract...");
    Materialize.toast('Publishing on the blockchain', 3000);
    $("#titleAction").html("We are publishing on the blockchain...");
    console.log(randomString);
    App.contracts.WHC.new(randomString, { value: web3.toWei(bounty, "ether"), gas: 600000 }).then(function (instance) {
      $("#titleAction").html("It is now published on the blockchain, we are publishing it on our database...");
      //Ajax call to save the contract in bdd
      $.ajax({
        url: Routing.generate('of_contracts_new_contract_ajax'),
        method: "post",
        data: { bounty: bounty, difficulty: difficulty, name: name, address: instance.address, company: company, description: description }
      }).done(function () {
        $("#titleAction").html('It is done :) <br /> <br /> Your slashcode is <b class="slashcodeAnnouncement">' + randomString + '</b>.<br /> Keep it safe, our team will actively look for it.');
        Materialize.toast('Published on BDD', 3000);


      });
    });
  },
  tryPass: function () {

    $('#testInput').hide();
    $('#loadingTestCode').show();
    $('#etape1').show();
    var pass = $("#passField").val();
    var address = $("#address").val();
    console.log(pass);
    console.log(address);
    console.log("loading the contract");
    $('#etape1').hide();
    $('#etape2').show();
    App.contracts.WHC
      .at(address) //Address of the contract
      .then(instance => {

        console.log("testing the pass");
        instance.passAttempt(pass).then(function (result, error) {
          $('#etape2').hide();
          $('#etape3').show();
          console.log(error);


          var result = (result.logs[0].args.message.c[0]);
          if (result == 1010) {
            $('#etape3').hide();
            $('#etape5').show();
            console.log("The pass is not correct :(");

          } else if (result == 1111) {
            $('#etape3').hide();
            $('#etape4').show();
            console.log("The pass is correct :)");
            App.disableContract();
          }
          else {
            console.log("Erreur dans la réponse");
          }
        });
      });

  },
  disableContract: function(){
    $id = $("#firstSection").attr("contract");
    $.ajax({
      url: Routing.generate('of_contracts_disable_contract_ajax'),
      method: "post",
      data: { id: $id }
    }).done(function () {
      alert('effacé');

    });

  },
  resetTry: function () {
    $('#etape4').hide();
    $('#etape5').hide();
    $('#loadingTestCode').hide();
    $('#testInput').show();

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


