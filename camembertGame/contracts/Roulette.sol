pragma solidity ^0.4.2;
import "./IterableMapping.sol";
contract Roulette {



    uint public countBets;
    uint public maxValue = 10 ether;
    // Just to verify that contracts are created only by CEO. Useless ?
    address public ceoAddress = 0x0;
    address public last_winner = 0x0;
    

    // Store the address for each bet. (adress => (uint, uint)).
    // In the first uint is the bet of the address. 
    // In the second one is the sum = bet + previous(sum)
    IterableMapping.itmap public ownerToBet;


    event Finished(address winner, uint randomNumber );

    //Used to debug
    event Log(string message, uint randomNumber, bytes32 blockHash, uint blockNumber);
    


    // Modifier
    modifier onlyCEO() {
        require(msg.sender == ceoAddress);
        _;
    }
    modifier launchable(){
        require(countBets >= maxValue);
        _;
    }
    
    modifier transactionMustContainEther() {
        require(msg.value > 0);
        _;
    }
    // Useless ?
    function getUserBet() public returns(uint value, uint sumBet){
        return (ownerToBet.data[msg.sender].value,   ownerToBet.data[msg.sender].betSum);
    }

    function Roulette() public{
        countBets = 0;
        
    }

    function bet()  payable  public transactionMustContainEther(){
        uint bet_val = msg.value;
        address bet_sender = msg.sender;
        // If the countbets is equal or bigger than the stop value, we launch the Roulette.
        // If the value is too high, we still launch the Roulette but we consider that he 
        // gave just the difference. 
        if (countBets + bet_val >= maxValue ){
            // Add the bet
            countBets += bet_val;

            // il faudra rajouter le cas où il avait déjà parier.

            IterableMapping.insert(ownerToBet, bet_sender, bet_val - (maxValue-(countBets + bet_val)), maxValue);
            
            // Launch the Roulette
            launch();
        }else{
              // il faudra rajouter le cas où il avait déjà parier.
            countBets+= bet_val;
            IterableMapping.insert(ownerToBet, bet_sender, bet_val, countBets);
            
        }
    }
    function launch() public launchable() {
        // Pick a number semi random. ( * 10 and /10 to have a decimal)
        // Fkcing shit : miners can cheat, and players too :( FML )
        uint randomNumber = (uint256(block.blockhash(block.number - 1))) % (maxValue ) ;
        Log("Random", randomNumber, block.blockhash(block.number - 1), block.number - 1);
        address winner = 0x0;
        for (var i = IterableMapping.iterate_start(ownerToBet); IterableMapping.iterate_valid(ownerToBet, i); i = IterableMapping.iterate_next(ownerToBet, i))
        {
            
            var (key, value, betSum) = IterableMapping.iterate_get(ownerToBet, i);
            
            if (winner == 0x0 && randomNumber <= betSum ){
                
                winner = key;
            }
        }
        require(winner.send(maxValue));
        
        last_winner =winner;
        Finished(winner,randomNumber );
    }



    function testHack(bytes32 hashcode) public returns(uint256) {
        return uint256(hashcode)%maxValue;
    }

}