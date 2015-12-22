<?
	class CGEM24 extends IPSModule {
		public function __construct($InstanceID) {

			//Never delete this line!
			parent::__construct($InstanceID);

			}
		
		public function Create() {
		
			//Never delete this line!
			parent::Create();
			
			$this->ConnectParent("{A5F663AB-C400-4FE5-B207-4D67CC030564}");
			
			$this->RegisterPropertyInteger("Poller", 0);
			$this->RegisterPropertyInteger("Phase", 1);
			
			$this->RegisterTimer("Poller", 0, "CGEM24_RequestRead(\$_IPS['TARGET']);");
 
		}

		public function ApplyChanges() {
			//Never delete this line!
			parent::ApplyChanges();
			
			$this->RegisterVariableFloat("Volt", "Volt", "Volt.230", 1);
			$this->RegisterVariableFloat("Ampere", "Ampere", "Ampere.16", 2);
			$this->RegisterVariableFloat("Watt", "Watt", "Watt.14490", 3);
			$this->RegisterVariableFloat("kWh", "Total kWh", "Electricity", 4);
			
			$this->SetTimerInterval("Poller", $this->ReadPropertyInteger("Poller"));
			
		}

		public function RequestRead() {
			
			$Address = 0x00 + ($this->ReadPropertyInteger("Phase") - 1)*2;
			$Volt = $this->SendDataToParent(json_encode(Array("DataID" => "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}", "Function" => 3, "Address" => $Address , "Quantity" => 2, "Data" => "")));
			$Volt = (unpack("n*", substr($Volt,2)));
			
			$Address = 0x0C + ($this->ReadPropertyInteger("Phase") - 1)*2;
			$Ampere = $this->SendDataToParent(json_encode(Array("DataID" => "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}", "Function" => 3, "Address" => $Address , "Quantity" => 2, "Data" => "")));
			$Ampere = (unpack("n*", substr($Ampere,2)));
			
			$Address = 0x12 + ($this->ReadPropertyInteger("Phase") - 1)*2;
			$Watt = $this->SendDataToParent(json_encode(Array("DataID" => "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}", "Function" => 3, "Address" => $Address , "Quantity" => 2, "Data" => "")));
			$Watt = (unpack("n*", substr($Watt,2)));
			
			$Address = 0x46 + ($this->ReadPropertyInteger("Phase") - 1)*2;
			$KWh = $this->SendDataToParent(json_encode(Array("DataID" => "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}", "Function" => 3, "Address" => $Address , "Quantity" => 2, "Data" => "")));
			$KWh = (unpack("n*", substr($KWh,2)));

			if(IPS_GetProperty(IPS_GetInstance($this->InstanceID)['ConnectionID'], "SwapWords")) {
				SetValue($this->GetIDForIdent("Volt"), ($Volt[1] + ($Volt[2] << 16))/10);
				SetValue($this->GetIDForIdent("Ampere"), ($Ampere[1] + ($Ampere[2] << 16))/1000);
				SetValue($this->GetIDForIdent("Watt"), ($Watt[1] + ($Watt[2] << 16))/10);
				SetValue($this->GetIDForIdent("kWh"), ($KWh[1] + ($KWh[2] << 16))/10);
			} else {
				SetValue($this->GetIDForIdent("Volt"), ($Volt[2] + ($Volt[1] << 16))/10);
				SetValue($this->GetIDForIdent("Ampere"), ($Ampere[2] + ($Ampere[1] << 16))/1000);
				SetValue($this->GetIDForIdent("Watt"), ($Watt[2] + ($Watt[1] << 16))/10);
				SetValue($this->GetIDForIdent("kWh"), ($KWh[2] + ($KWh[1] << 16))/10);
			}
			
		}

		
	}
?>